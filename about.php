<?php
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>About OneTap Bus</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
}

.about-hero {
    position: relative;
    width: 100%;
    height: 888px;
    overflow: hidden;
}

.about-hero::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(
        90deg,
        rgba(0,0,0,0.75) 0%,
        rgba(0,0,0,0.45) 40%,
        rgba(0,0,0,0.15) 70%,
        rgba(0,0,0,0) 100%
    );
    z-index: 1;
}

.about-hero::before,
.about-hero::after {
    pointer-events: none;
}

.about-hero::after {
    background:
        linear-gradient(
            to bottom,
            rgba(0,0,0,0.75) 0%,
            rgba(0,0,0,0.45) 40%,
            rgba(0,0,0,0.15) 65%,
            #171718 100%
        );
}

.about-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background: url("assets/images/about-hero.jpg") center / cover no-repeat;
    transform: scale(1) translate(0, 0);
    transition: transform 0.4s ease-out;
    will-change: transform;
    z-index: 0;
}

.about-hero:hover::before {
    transform: scale(1.1) translate(var(--x), var(--y));
}

.about-content {
    position: absolute;
    top: 160px;
    left: 64px;
    max-width: 640px;
    color: #ffffff;
    z-index: 2;
}

.about-content h1 {
    font-weight: 800;
    font-size: 40px;
    line-height: 1.2;
    margin-bottom: 22px;

    -webkit-text-stroke: 1.5px #000;
    text-shadow: 0 3px 6px rgba(0,0,0,0.45);
}

.about-content p {
    font-size: 16px;
    line-height: 1.75;
    opacity: 0.95;
}

.about-contact {
    margin-top: 28px;
    font-size: 14px;        
    line-height: 1.6;
    opacity: 0.9;
}

.about-contact strong {
    display: block;
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 6px;
}

.about-contact a {
    color: #ffffff;
    text-decoration: underline;
}

.team-section {
    padding: 80px 20px;
    text-align: center;
    background: linear-gradient(180deg, #171718 0%, #0c0c0e 100%);
}

.team-section h2 {
    font-size: 28px;
    font-weight: 800;
    margin-bottom: 50px;
    color: #ffffffff;
}

.team-grid {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 40px;
    transition: all 0.5s ease;
}


.team-member {
    flex: 1;
    max-width: 220px;
    transition: 
        flex 0.5s ease,
        transform 0.5s ease,
        opacity 0.5s ease;
    cursor: pointer;
}

.team-grid:hover .team-member {
    opacity: 0.55;
    transform: scale(0.9);
}

.team-grid .team-member:hover {
    flex: 2;
    opacity: 1;
    transform: scale(1.15);
    z-index: 2;
}


.team-member img {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    object-fit: cover;

   
    padding: 4px;
    background: #171718ff;
    border: 1.5px solid #ffffff;

    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    margin-bottom: 18px;
    transition: transform 0.5s ease;
}


.team-member h4 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 6px;
    color: #ffffffff;
    transition: transform 0.4s ease, letter-spacing 0.4s ease;
}

.team-member:hover h4 {
    transform: scale(1.1);
    letter-spacing: 0.5px;
}

.team-member p {
    font-size: 14px;
    opacity: 0.75;
}

.team-member:hover img {
    transform: scale(1.08);
}

.complaint-footer {
    background: #000;
    color: #fff;
    text-align: center;
    padding: 20px;
    font-size: 14px;
}

.complaint-footer a {
    color: #4f6ef7;
    text-decoration: underline;
}

@media (max-width: 768px) {
    .site-footer {
        padding: 40px 20px 24px;
    }

    .footer-inner {
        grid-template-columns: 1fr;
        gap: 28px;
    }

    .footer-bottom {
        font-size: 12px;
    }
}


@media (max-width: 768px) {
    .about-hero {
        height: 460px;
    }

    .about-content {
        top: 120px;
        left: 20px;
        max-width: 90%;
    }

    .about-content h1 {
        font-size: 26px;
    }

    .about-content p {
        font-size: 14px;
    }
}
@media (hover: none) {
    .about-hero:hover::before {
        transform: scale(1.05);
    }
}

</style>
</head>

<body>

<section class="about-hero">
    <div class="about-content">
        <h1>About OneTap Bus</h1>
        <p>
            One Tap Bus is a simple and reliable bus search and fare information platform
            designed for daily commuters in Dhaka. It helps users quickly find available
            bus routes, bus names, and estimated fares between two locations without any
            confusions.
            <br><br>
            Built with local routes and real commuting needs in mind, One Tap Bus aims to
            make public transport information easier to access, faster to understand, and
            practical for everyday travel across Dhaka.
        </p>
        <br><br>
    </div>

</section>
<section class="team-section">
    <h2>Meet Our Team</h2>

    <div class="team-grid">
        <div class="team-member">
            <img src="assets/images/team1.jpg">
            <h4>Sumaiya Islam Aritra</h4>
        </div>

        <div class="team-member">
            <img src="assets/images/team2.jpg">
            <h4>Tahmid Islam</h4>
        </div>

        <div class="team-member">
            <img src="assets/images/team3.jpg">
            <h4>Yeanul Haque Khan Akib</h4>
        </div>
    </div>
</section>

<script>
const aboutHero = document.querySelector('.about-hero');

aboutHero.addEventListener('mousemove', (e) => {
    const rect = aboutHero.getBoundingClientRect();

    const x = (e.clientX - rect.left) / rect.width - 0.5;
    const y = (e.clientY - rect.top) / rect.height - 0.5;

    const moveX = x * 20;
    const moveY = y * 20;

    aboutHero.style.setProperty('--x', `${moveX}px`);
    aboutHero.style.setProperty('--y', `${moveY}px`);
});

aboutHero.addEventListener('mouseleave', () => {
    aboutHero.style.setProperty('--x', `0px`);
    aboutHero.style.setProperty('--y', `0px`);
});
</script>
<footer class="complaint-footer">
    <p>
        Any complaints or issues?  
        Email us at <a href="mailto:support@example.com">support@example.com</a>
    </p>
</footer>
</body>
</html>
