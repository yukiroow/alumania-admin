<?php
session_start(); 

// Handle logout request
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../../");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="stylesheet" href="../../res/styles/settings.css">
    <title>Alumania</title>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <script defer> setActiveNav("settingstab", "settingsicon", 6); </script>
    
<div class="content-container">
    <div class="header">
        <h1>Settings</h1>
        <p>Logout or generate a new admin key</p>
    </div>

    <main class="main-content">
        <div class="logout">
            <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script> 
            <dotlottie-player id="logout-animation"src="https://lottie.host/f7a742ba-808a-4b0c-b29f-ca0d1e985b30/0utrtWPYc3.json" background="transparent" speed="1"></dotlottie-player>
            <p>Logout</p>
            <div class="popup" id="popup-logout">
                <div class="overlay" onclick="togglePopup('popup-logout')"></div> 
                <div class="content-logout" onclick="event.stopPropagation()">
                    <div class="close-btn" onclick="togglePopup('popup-logout')">&times;</div>
                    <h1>Confirm Logout</h1>
                    <hr>
                    <p>Are you sure you want to log out?</p>
                    <div class="button-container">
                        <button class="cancel-btn">Cancel</button>
                        <form method="POST">
                            <button class="ok-btn" type="submit" name="logout">Logout</form></button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="generate">
            <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script> 
            <dotlottie-player id="generate-animation" src="https://lottie.host/1c7b457d-d7a7-4aac-98f9-d8f5c0ab90e7/cK1MYzB7Cw.json" background="transparent" speed="1"></dotlottie-player>
            <p>Generate new admin key</p>
            <div class="popup" id="popup-generate">
                <div class="overlay" onclick="togglePopup('popup-generate')"></div>
                <div class="content-generate" onclick="event.stopPropagation()">
                    <div class="close-btn" onclick="closeAndClearInput()">&times;</div>
                    <h1>Generate new admin key</h1>
                    <hr>
                    <div class="input-container">
                        <p>Please save your new admin key in a secure location</p>
                        <div class="copy-text">
                            <input type="text" id="generatedKey" class="text" value="" style="width: 100%;" readonly>
                            <button class="copy-btn" onclick="copyToClipboard()">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="20" height="20">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15 1.25H10.9436C9.10583 1.24998 7.65019 1.24997 6.51098 1.40314C5.33856 1.56076 4.38961 1.89288 3.64124 2.64124C2.89288 3.38961 2.56076 4.33856 2.40314 5.51098C2.24997 6.65019 2.24998 8.10582 2.25 9.94357V16C2.25 17.8722 3.62205 19.424 5.41551 19.7047C5.55348 20.4687 5.81753 21.1208 6.34835 21.6517C6.95027 22.2536 7.70814 22.5125 8.60825 22.6335C9.47522 22.75 10.5775 22.75 11.9451 22.75H15.0549C16.4225 22.75 17.5248 22.75 18.3918 22.6335C19.2919 22.5125 20.0497 22.2536 20.6517 21.6517C21.2536 21.0497 21.5125 20.2919 21.6335 19.3918C21.75 18.5248 21.75 17.4225 21.75 16.0549V10.9451C21.75 9.57754 21.75 8.47522 21.6335 7.60825C21.5125 6.70814 21.2536 5.95027 20.6517 5.34835C20.1208 4.81753 19.4687 4.55348 18.7047 4.41551C18.424 2.62205 16.8722 1.25 15 1.25ZM17.1293 4.27117C16.8265 3.38623 15.9876 2.75 15 2.75H11C9.09318 2.75 7.73851 2.75159 6.71085 2.88976C5.70476 3.02502 5.12511 3.27869 4.7019 3.7019C4.27869 4.12511 4.02502 4.70476 3.88976 5.71085C3.75159 6.73851 3.75 8.09318 3.75 10V16C3.75 16.9876 4.38624 17.8265 5.27117 18.1293C5.24998 17.5194 5.24999 16.8297 5.25 16.0549V10.9451C5.24998 9.57754 5.24996 8.47522 5.36652 7.60825C5.48754 6.70814 5.74643 5.95027 6.34835 5.34835C6.95027 4.74643 7.70814 4.48754 8.60825 4.36652C9.47522 4.24996 10.5775 4.24998 11.9451 4.25H15.0549C15.8297 4.24999 16.5194 4.24998 17.1293 4.27117ZM7.40901 6.40901C7.68577 6.13225 8.07435 5.9518 8.80812 5.85315C9.56347 5.75159 10.5646 5.75 12 5.75H15C16.4354 5.75 17.4365 5.75159 18.1919 5.85315C18.9257 5.9518 19.3142 6.13225 19.591 6.40901C19.8678 6.68577 20.0482 7.07435 20.1469 7.80812C20.2484 8.56347 20.25 9.56458 20.25 11V16C20.25 17.4354 20.2484 18.4365 20.1469 19.1919C20.0482 19.9257 19.8678 20.3142 19.591 20.591C19.3142 20.8678 18.9257 21.0482 18.1919 21.1469C17.4365 21.2484 16.4354 21.25 15 21.25H12C10.5646 21.25 9.56347 21.2484 8.80812 21.1469C8.07435 21.0482 7.68577 20.8678 7.40901 20.591C7.13225 20.3142 6.9518 19.9257 6.85315 19.1919C6.75159 18.4365 6.75 17.4354 6.75 16V11C6.75 9.56458 6.75159 8.56347 6.85315 7.80812C6.9518 7.07435 7.13225 6.68577 7.40901 6.40901Z" fill="#fff"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button class="gen-btn"  onclick="generateKey()">Generate Admin Key </button>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="../js/settings.js"></script>
<script>
// COPY GENERATED ADMIN KEY
function copyToClipboard() {
    const input = document.querySelector('.text');
    input.select();
    navigator.clipboard.writeText(input.value).then(() => {
        alert('Admin key copied to clipboard!');
    }).catch(err => {
        console.error('Error copying text: ', err);
    });
}

function generateKey() {
    const length = 16;

    fetch(`generateKey.php?length=${length}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch key');
            }
            return response.text();
        })
        .then(generatedKey => {
            document.getElementById("generatedKey").value = generatedKey;
            updatePasswordInDatabase(generatedKey);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function updatePasswordInDatabase(newPassword) {

    const data = new URLSearchParams();
    data.append('password', newPassword);

    fetch('updatePassword.php', {
        method: 'POST',
        body: data, 
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded' 
        }
    })
    .then(response => {
        if (response.ok) {
            console.log('Password updated successfully');
        } else {
            console.error('Failed to update password:', response.status);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

</script>
<script src="../js/contentmove.js"></script>

</body>

</html>
