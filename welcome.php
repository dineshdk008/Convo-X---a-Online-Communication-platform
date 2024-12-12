<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to ChatApp</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100%;
            width: 100%;
            background-color: #f7f7f7;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color:black;
            color: white;
        }
        header h1 {
            font-size: 2.5em;
            margin: 0;
            color:pink;
            cursor: pointer;
            font-family:'Venite Adoremus Straight';
        }
        header nav {
            display: flex;
            align-items: center;
        }
        header nav a {
            color: white;
            text-decoration: none;
            margin-left: 30px;
            font-size: 1.2em;
            font-weight: bold;
        }
        header nav a:hover {
            text-decoration: underline;
        }
        main {
            flex-grow: 1;
            text-align: center;
            padding: 50px 20px;
            background-color: #FAF3DD;
        }
        h2 {
            font-size: 2.0em;
            margin-bottom: 20px;
            color: #202124;
        }
        .role-options form {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .role-button {
            padding: 15px 30px;
            font-size: 1.2em;
            background-color: #202124;
            color: white;
            border: none;
            border-radius: 100px;
            cursor: pointer;
            width: 200px;
        }
        .role-button:hover {
            background-color: #FFD700;
            color: #202124;
        }
        .language-selection {
            margin-top: 20px;
        }
        .language-selection select {
            padding: 10px;
            font-size: 0.9em;
            border-radius: 30px;
            color:white;
            background-color: black;
        }
        .news-section {
            margin-top: 30px;
            font-size: 1.1em;
            text-align: center;
        }
        .news-section ul {
            list-style: none;
            padding-left: 0;
            text-align: left;
            display: inline-block;
        }
        .news-section li {
            margin-bottom: 10px;
        }
        .social-media a {
            color: #007bff;
            text-decoration: none;
            margin: 0 10px;
        }
        .social-media a:hover {
            text-decoration: underline;
        }
        .app-download a {
            color: #007bff;
            text-decoration: none;
            margin: 0 10px;
        }
        .app-download a:hover {
            text-decoration: underline;
        }
        footer {
            background-color: #222;
            color: white;
            text-align: center;
            padding: 10px 20px;
            font-size: 1em;
        }
        .footer-links a {
            color: skyblue;
            text-decoration: none;
            margin: 0 10px;
        }
        .footer-links a:hover {
            text-decoration: underline;
        }
        .logo {
        height: 40px;
        width: auto;
        margin-right: 10px; 
    }
    .logo-title {
        display: flex;
        align-items: center; 
    }
    </style>
    <script>
        const translations = {
            en: {
                header: "Convo X",
                welcomeNew: "Welcome to Convo X",
                selectLanguage: "Select Language",
                latestUpdates: "LATEST UPDATES!!",
                updates: [
                    "New feature: Polls and Surveys 📊",
                    "Improved notifications for group chats ⚡",
                    "Bug fixes and performance enhancements 🚀"
                ],
                connectWithUs: "Connect with us:",
                downloadApp: "Download the ChatApp",
            },
            es: {
                header: "Convo X",
                welcomeNew: "Bienvenido a Convo X",
                selectLanguage: "Seleccionar idioma",
                latestUpdates: "¡ÚLTIMAS ACTUALIZACIONES!",
                updates: [
                    "Nueva función: Encuestas y Sondeos 📊",
                    "Notificaciones mejoradas para chats grupales ⚡",
                    "Corrección de errores y mejoras de rendimiento 🚀"
                ],
                connectWithUs: "Conéctate con nosotros:",
                downloadApp: "Descarga la aplicación ChatApp",
            },
            fr: {
                header: "Convo X",
                welcomeNew: "Bienvenue sur Convo X",
                selectLanguage: "Choisir la langue",
                latestUpdates: "DERNIÈRES MISES À JOUR !!",
                updates: [
                    "Nouvelle fonctionnalité : Sondages et enquêtes 📊",
                    "Notifications améliorées pour les discussions de groupe ⚡",
                    "Corrections de bugs et améliorations des performances 🚀"
                ],
                connectWithUs: "Connectez-vous avec nous :",
                downloadApp: "Téléchargez l'application ChatApp",
            },
            de: {
                header: "Convo X",
                welcomeNew: "Willkommen bei Convo X",
                selectLanguage: "Sprache wählen",
                latestUpdates: "NEUESTE UPDATES!!",
                updates: [
                    "Neue Funktion: Umfragen und Abstimmungen 📊",
                    "Verbesserte Benachrichtigungen für Gruppenchats ⚡",
                    "Fehlerbehebungen und Leistungsverbesserungen 🚀"
                ],
                connectWithUs: "Verbinde dich mit uns:",
                downloadApp: "Laden Sie die ChatApp herunter",
            }
        };
        function changeLanguage() {
            const selectedLanguage = document.getElementById("language-select").value;
            const translation = translations[selectedLanguage];
            document.getElementById("header-title").textContent = translation.header;
            document.getElementById("welcome").textContent = translation.welcomeNew;
            document.getElementById("select-language").textContent = translation.selectLanguage;
            document.getElementById("latest-updates").textContent = translation.latestUpdates;
            const updatesList = document.getElementById("updates-list");
            updatesList.innerHTML = "";
            translation.updates.forEach(update => {
                const li = document.createElement("li");
                li.textContent = update;
                updatesList.appendChild(li);
            });
            document.getElementById("connect-with-us").textContent = translation.connectWithUs;
            document.getElementById("download-app").textContent = translation.downloadApp;
        }
    </script>
</head>
<body>
    <header>
    <div class="logo-title">
    <img src="chatgpt.jpg" alt="Logo" class="logo"> 
        <h1 id="header-title" >Convo X</h1>
</div>
        <nav>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    </header>

    <main>
        <h2 id="welcome" style="font-family:'Venite Adoremus Straight'">Welcome to Convo X</h2>
        <div class="language-selection">
            <h3 id="select-language">Select Language</h3>
            <select id="language-select" onchange="changeLanguage()">
                <option value="en">English</option>
                <option value="es">Español</option>
                <option value="fr">Français</option>
                <option value="de">Deutsch</option>
            </select>
        </div>
        <section class="news-section">
            <h4 id="latest-updates">LATEST UPDATES!!</h4>
            <ul id="updates-list">
                <li style="text-align:center";>New feature: Polls and Surveys 📊</li>
                <li style="text-align:center";>Improved notifications for group chats⚡</li>
                <li style="text-align:center";>Bug fixes and performance enhancements 🚀</li>
            </ul>
        </section>
        <section class="social-media">
            <h4 id="connect-with-us">Connect with us:</h4>
            <a href="https://facebook.com" target="_blank">Facebook</a> |
            <a href="https://twitter.com" target="_blank">Twitter</a> |
            <a href="https://instagram.com" target="_blank">Instagram</a>
        </section>
        <section class="app-download">
            <h4 id="download-app">Download the ChatApp</h4>
            <a href="https://play.google.com/store" target="_blank">Google Play</a> |
            <a href="https://apps.apple.com" target="_blank">App Store</a>
        </section>
    </main>
    <footer>
    <div class="footer-links">
        <a href="#">About Us</a>
        <a href="#">Privacy Policy</a>
        <a href="#">Contact Support</a>
    </div>
        <p>&copy; 2024 Convo X. All rights reserved.</p>
    </footer>
</body>
</html>
