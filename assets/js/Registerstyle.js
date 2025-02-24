document.addEventListener("DOMContentLoaded", function () {
    var animation = lottie.loadAnimation({
        container: document.getElementById('lottie-animation'),
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: 'assets/Animation - 1739314661005.json' 
    });

    document.getElementById("register-form").addEventListener("submit", function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch("register.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.includes("successful")) {
                document.getElementById("success-message").classList.remove("d-none");
                document.getElementById("success-message").innerHTML = data;
                document.getElementById("error-message").classList.add("d-none");
                this.reset();
            } else {
                document.getElementById("error-message").classList.remove("d-none");
                document.getElementById("error-message").innerHTML = data;
                document.getElementById("success-message").classList.add("d-none");
            }
        })
        .catch(error => {
            document.getElementById("error-message").classList.remove("d-none");
            document.getElementById("error-message").innerHTML = "Something went wrong!";
        });
    });
});
