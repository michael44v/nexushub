<?php
// ---------- Keccak-256 (pure PHP, 64-bit) ----------
function rotl64($x, $n) {
    return ($x << $n) | (($x >> (64 - $n)) & ~(-1 << $n));
}

function keccakf(array &$st) {
    static $RC = null;
    if ($RC === null) {
        $RC = [];
        foreach ([
            '0000000000000001','0000000000008082','800000000000808a','8000000080008000',
            '000000000000808b','0000000080000001','8000000080008081','8000000000008009',
            '000000000000008a','0000000000000088','0000000080008009','000000008000000a',
            '000000008000808b','800000000000008b','8000000000008089','8000000000008003',
            '8000000000008002','8000000000000080','000000000000800a','800000008000000a',
            '8000000080008081','8000000000008080','0000000080000001','8000000080008008'
        ] as $h) { $RC[] = unpack('J', hex2bin($h))[1]; }
    }
    $rotc = [1,3,6,10,15,21,28,36,45,55,2,14,27,41,56,8,25,43,62,18,39,61,20,44];
    $piln = [10,7,11,17,18,3,5,16,8,21,24,4,15,23,19,13,12,2,20,14,22,9,6,1];

    for ($r = 0; $r < 24; $r++) {
        $bc = [];
        for ($i = 0; $i < 5; $i++)
            $bc[$i] = $st[$i] ^ $st[$i+5] ^ $st[$i+10] ^ $st[$i+15] ^ $st[$i+20];
        for ($i = 0; $i < 5; $i++) {
            $t = $bc[($i+4) % 5] ^ rotl64($bc[($i+1) % 5], 1);
            for ($j = 0; $j < 25; $j += 5) $st[$j+$i] ^= $t;
        }
        $t = $st[1];
        for ($i = 0; $i < 24; $i++) {
            $j = $piln[$i];
            $tmp = $st[$j];
            $st[$j] = rotl64($t, $rotc[$i]);
            $t = $tmp;
        }
        for ($j = 0; $j < 25; $j += 5) {
            for ($i = 0; $i < 5; $i++) $bc[$i] = $st[$j+$i];
            for ($i = 0; $i < 5; $i++) $st[$j+$i] ^= (~$bc[($i+1) % 5]) & $bc[($i+2) % 5];
        }
        $st[0] ^= $RC[$r];
    }
}

function keccak256(string $data): string { // returns hex
    $rate = 136;
    $data .= "\x01";
    $data .= str_repeat("\0", ($rate - strlen($data) % $rate) % $rate);
    $last = strlen($data) - 1;
    $data[$last] = chr(ord($data[$last]) | 0x80);

    $st = array_fill(0, 25, 0);
    for ($off = 0; $off < strlen($data); $off += $rate) {
        $lanes = unpack('P17', substr($data, $off, $rate));
        for ($i = 0; $i < 17; $i++) $st[$i] ^= $lanes[$i+1];
        keccakf($st);
    }
    return bin2hex(pack('P4', $st[0], $st[1], $st[2], $st[3]));
}

// ---------- secp256k1 public key recovery (GMP) ----------
function ec_add($P, $Q, $p) {
    if ($P === null) return $Q;
    if ($Q === null) return $P;
    if (gmp_cmp($P[0], $Q[0]) == 0) {
        if (gmp_cmp(gmp_mod(gmp_add($P[1], $Q[1]), $p), 0) == 0) return null;
        $l = gmp_mod(gmp_mul(gmp_mul(3, gmp_mul($P[0], $P[0])), gmp_invert(gmp_mul(2, $P[1]), $p)), $p);
    } else {
        $l = gmp_mod(gmp_mul(gmp_sub($Q[1], $P[1]), gmp_invert(gmp_mod(gmp_sub($Q[0], $P[0]), $p), $p)), $p);
    }
    $x = gmp_mod(gmp_sub(gmp_sub(gmp_mul($l, $l), $P[0]), $Q[0]), $p);
    $y = gmp_mod(gmp_sub(gmp_mul($l, gmp_sub($P[0], $x)), $P[1]), $p);
    return [$x, $y];
}

function ec_mul($k, $P, $p) {
    $R = null;
    foreach (str_split(gmp_strval($k, 2)) as $bit) {
        $R = ec_add($R, $R, $p);
        if ($bit === '1') $R = ec_add($R, $P, $p);
    }
    return $R;
}

function eth_recover_address(string $message, string $signature): ?string {
    if (!preg_match('/^0x[0-9a-fA-F]{130}$/', $signature)) return null;

    $p  = gmp_init('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F', 16);
    $n  = gmp_init('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141', 16);
    $G  = [gmp_init('79BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798', 16),
           gmp_init('483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8', 16)];

    $r = gmp_init(substr($signature, 2, 64), 16);
    $s = gmp_init(substr($signature, 66, 64), 16);
    $v = hexdec(substr($signature, 130, 2));
    $recid = $v >= 27 ? $v - 27 : $v;
    if ($recid > 1) return null;
    if (gmp_cmp($r, 0) <= 0 || gmp_cmp($r, $n) >= 0 || gmp_cmp($s, 0) <= 0 || gmp_cmp($s, $n) >= 0) return null;

    $prefixed = "\x19Ethereum Signed Message:\n" . strlen($message) . $message;
    $e = gmp_init(keccak256($prefixed), 16);

    // Recover point R
    $y2 = gmp_mod(gmp_add(gmp_powm($r, 3, $p), 7), $p);
    $y  = gmp_powm($y2, gmp_div_q(gmp_add($p, 1), 4), $p);
    if (gmp_cmp(gmp_mod(gmp_mul($y, $y), $p), $y2) != 0) return null;
    if ((int)gmp_strval(gmp_mod($y, 2)) !== $recid) $y = gmp_sub($p, $y);
    $R = [$r, $y];

    // Q = r^-1 (sR - eG)
    $sR = ec_mul($s, $R, $p);
    $eG = ec_mul($e, $G, $p);
    if ($sR === null || $eG === null) return null;
    $sum = ec_add($sR, [$eG[0], gmp_sub($p, $eG[1])], $p);
    if ($sum === null) return null;
    $Q = ec_mul(gmp_invert($r, $n), $sum, $p);
    if ($Q === null) return null;

    $pub = str_pad(gmp_strval($Q[0], 16), 64, '0', STR_PAD_LEFT)
         . str_pad(gmp_strval($Q[1], 16), 64, '0', STR_PAD_LEFT);
    return '0x' . substr(keccak256(hex2bin($pub)), -40);
}