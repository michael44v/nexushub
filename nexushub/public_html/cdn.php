<?php
//session_start();
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a0a0a">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Legacy Support (some pages might still use these) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316',
                        secondary: '#fbbf24',
                        dark: '#0a0a0a',
                        gold: '#fbbf24',
                    },
                    fontFamily: {
                        syne: ['Syne', 'sans-serif'],
                        sans: ['DM Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --primary: #f97316;
            --gold: #fbbf24;
            --dark: #0a0a0a;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: #000;
            color: #fff;
        }
        .font-syne { font-family: 'Syne', sans-serif; }
        .gradient-text {
            background: linear-gradient(135deg, #f97316, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .bg-gradient-gold {
            background: linear-gradient(135deg, #f97316 0%, #fbbf24 100%);
        }
        .border-gold {
            border-color: rgba(251, 191, 36, 0.3);
        }
        .card-dark {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #000;
        }
        ::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #444;
        }

        /* WhatsApp Float Legacy */
        .float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 20px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
        }
        .my-float {
            margin-top: 16px;
        }
    </style>
</head>

<a href="https://api.whatsapp.com/send?phone=+85259970400&text=Hello Admin, i am from Virtual-Sync, my name is _____" class="float" target="_blank">
    <i class="fab fa-whatsapp my-float"></i>
</a>
