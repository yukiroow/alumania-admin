const logoutAnimation = document.getElementById("logout-animation");
const generateAnimation = document.getElementById("generate-animation");
const overlay = document.querySelector(".overlay");
const cancelBtn = document.querySelector(".cancel-btn");

document.querySelector(".logout").addEventListener("mouseenter", () => {
  logoutAnimation.setAttribute("loop", "true");
  logoutAnimation.play();
});

document.querySelector(".logout").addEventListener("mouseleave", () => {
  logoutAnimation.removeAttribute("loop");
});

document.querySelector(".generate").addEventListener("mouseenter", () => {
  generateAnimation.setAttribute("loop", "true");
  generateAnimation.play();
});

document.querySelector(".generate").addEventListener("mouseleave", () => {
  generateAnimation.removeAttribute("loop");
  generateAnimation.stop();
});

//POPUP
document
  .querySelector(".logout")
  .addEventListener("click", () => togglePopup("popup-logout"));
document
  .querySelector(".generate")
  .addEventListener("click", () => togglePopup("popup-generate"));
function togglePopup(popupId) {
  document.getElementById(popupId).classList.toggle("active");
}

// COPY GENERATED ADMIN KEY
function copyToClipboard() {
  const input = document.querySelector(".text");
  input.select();
  navigator.clipboard
    .writeText(input.value)
    .then(() => {
      alert("Admin key copied to clipboard!");
    })
    .catch((err) => {
      console.error("Error copying text: ", err);
    });
}

// Cancel button
cancelBtn.addEventListener("click", () => {
  togglePopup("popup-logout");
});

function changePassword() {
  const newPassword = document.getElementById("newPassword").value;
  const confirmPassword = document.getElementById("confirmPassword").value;

  if (newPassword === "" || confirmPassword === "") {
    alert("Please fill in both fields.");
    return;
  }

  if (newPassword !== confirmPassword) {
    alert("Passwords do not match.");
    return;
  }

  fetch("change_password.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({
      newPassword: newPassword,
    }).toString(),
  })
    .then((response) => response.text())
    .then((data) => {
      alert(data);
      if (data.includes("success")) {
        location.reload();
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while changing the password.");
    });
}
