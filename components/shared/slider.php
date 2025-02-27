<section class="slider mt-3">
    <div id="heroCarousel" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#heroCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#heroCarousel" data-slide-to="1"></li>
            <li data-target="#heroCarousel" data-slide-to="2"></li>
        </ol>

        <!-- Carousel Inner -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Shere_Bangla_National_Stadium.jpg/4928px-Mapcarta.jpg" class="d-block w-100" alt="Slide 1">
                <div class="carousel-caption d-none d-md-block">
                    <h3 class="text-uppercase font-weight-bold">Stay Updated</h3>
                    <p>Get the latest updates on your favorite matches and teams.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://cdn.britannica.com/51/190751-131-B431C216/soccer-ball-goal.jpg" class="d-block w-100" alt="Slide 2">
                <div class="carousel-caption d-none d-md-block">
                    <h3 class="text-uppercase font-weight-bold">Live Scores</h3>
                    <p>Never miss a moment with real-time scores.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://media.istockphoto.com/id/1402084914/photo/cricket-ball-on-top-of-cricket-bat-on-green-grass-of-cricket-ground-background.jpg?s=612x612&w=0&k=20&c=PSz4n0BZ8sJL9uN9K3zF3Ay7PBakRMf9uL5tkz7QJ_k=" class="d-block w-100" alt="Slide 3">
                <div class="carousel-caption d-none d-md-block">
                    <h3 class="text-uppercase font-weight-bold">Upcoming Events</h3>
                    <p>Check out schedules for upcoming matches.</p>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</section>

<style>
    .carousel-caption {
        background: rgba(0, 0, 0, 0.6);
        padding: 1rem 2rem;
        border-radius: 8px;
        animation: fadeInUp 1s ease-in-out;
    }

    .carousel-item img {
        object-fit: cover;
        height: 70vh;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        filter: invert(1);
    }

    @keyframes fadeInUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>
