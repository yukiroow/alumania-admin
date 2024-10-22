<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="stylesheet" href="../../res/styles/posts.css">
    <title>Alumania</title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="content-container">
        <main class="main-content">
            <div class="header">
                <h1>Posts</h1>
            </div>

            <div class="navigation">
                <ul id="ul-posts">
                    <li><a href="#"><img src="../../res/experience-posts-blue.png" alt="User experience">Experience</a>
                    </li>
                    <li><a href="#"><img src="../../res/calendar-posts.png" alt="Events">Events</a></li>
                    <li><a href="#"><img src="../../res/jlisting-posts.png" alt="Listing">Job Listing</a></li>
                </ul>
            </div>
        </main>
    </div>

    <script>
        const navbar = document.querySelector('.navbar');
        const navToggle = document.querySelector('.nav-toggle');
        const contentContainer = document.querySelector('.content-container');

        navToggle.addEventListener('click', () => {
            navbar.classList.toggle('collapsed');
            mainContent.classList.toggle('shifted');
        });  
    </script>

</body>

</html>
</body>