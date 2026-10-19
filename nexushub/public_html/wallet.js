import { BrowserProvider } from "https://cdn.jsdelivr.net/npm/ethers@6.13.4/+esm";

const btn = document.getElementById('connectBtn');
const statusEl = document.getElementById('walletStatus');
const short = a => a.slice(0, 6) + '...' + a.slice(-4);

async function init() {
  const { address: saved } = await (await fetch('wallet_status.php')).json();

  if (saved) {
    btn.style.display = 'none';
    statusEl.textContent = 'Wallet linked: ' + short(saved);

    // Silent check (no popup): is the same wallet connected in this browser?
    if (window.ethereum) {
      const accts = await window.ethereum.request({ method: 'eth_accounts' });
      if (accts.length && accts[0].toLowerCase() !== saved.toLowerCase()) {
        statusEl.textContent += ' (a different wallet is active in your browser)';
      }
    }
  } else {
    statusEl.textContent = 'No wallet linked';
  }
}

btn.onclick = async () => {
  if (!window.ethereum) return alert('Install MetaMask or open this site in your wallet browser');
  try {
    const provider = new BrowserProvider(window.ethereum);
    const signer = await provider.getSigner();           // popup
    const address = await signer.getAddress();

    const { message } = await (await fetch('wallet_nonce.php')).json();
    const signature = await signer.signMessage(message); // popup

    const res = await fetch('wallet_verify.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ signature })
    });
    const data = await res.json();

    if (data.ok) {
      statusEl.textContent = 'Wallet linked: ' + short(data.address);
      btn.style.display = 'none';
    } else {
      statusEl.textContent = 'Failed: ' + data.error;
    }
  } catch (e) {
    statusEl.textContent = 'Cancelled or failed';
  }
};

init();