<div class="side-bar">
    <a href="/"><div class="menu-header-big"><i class="fa fa-home" aria-hidden="true"></i>&emsp;Home</div></a>
    <a href="/agdi/threads.php"><div class="menu-header-big"><i class="fa fa-list" aria-hidden="true"></i>&emsp;Topics</div></a>
    <a href="<?php
                if (isset($_SESSION['id'])) { echo '/profile.php?id='.$_SESSION['id']; } else { echo '/login.php'; }
             ?>"><div class="menu-header-big"><i class="fas fa-user" aria-hidden="true"></i>&emsp;Profile</div></a>
    <div class="up-menu">
        <div class="menu-header dis"><i class="fab fa-hotjar" aria-hidden="true"></i>&emsp;Hot</div>
        <a href="#">
            <div class="mini-card">
                <div class="min-card-header">Artificial Intelligence</div>
                <div class="mini-card-text">On the nature of consciousness we must argue here that there are</div>
                <div class="footer-left">
                    <div class="count ags">10</div>
                    <div class="count dis">8</div>
                    <div class="count rep">12</div>
                </div>
            </div>
        </a>
        <a href="#">
            <div class="mini-card">
                <div class="min-card-header">Computer Science</div>
                <div class="mini-card-text">Lorem ipsum and shit, look at me, we have to</div>
                <div class="footer-left">
                    <div class="count ags">13</div>
                    <div class="count dis">2</div>
                    <div class="count rep">5</div>
                </div>
            </div>
        </a>
        <a href="#">
            <div class="mini-card">
                <div class="min-card-header">Physics</div>
                <div class="mini-card-text">Gravity is a lie! It is bullshit!</div>
                <div class="footer-left">
                    <div class="count ags">4</div>
                    <div class="count dis">3</div>
                    <div class="count rep">8</div>
                </div>
            </div>
        </a>
    </div>
</div>