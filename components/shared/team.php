<section id="team" style="padding: 3rem 0; background-color: #f1f1f1;">
    <div class="container">
        <div class="row text-center">
            <div class="col-12">
                <h2 style="text-transform: uppercase; font-weight: bold; margin-bottom: 2rem; color: #333;">
                    Meet Our Team
                </h2>
                <p style="margin-bottom: 2rem; font-size: 1.1rem; line-height: 1.8; color: #555;">
                    Our dedicated software engineering team is composed of bright minds, passionate about delivering top-notch
                    solutions while excelling in academics. Here's a glimpse of the talent behind the scenes.
                </p>
            </div>
        </div>
        <div class="row">
            <?php
            // Array of team members
            $team = [
                [
                    "name" => "Salah Uddin Selim",
                    "student_id" => "0112230512",
                    "semester" => "7th trimester",
                    "university" => "United Internatinal University",
                    "image" => "photos/selim.jpg"
                ],
                [
                    "name" => "Afia Tasnim Ria",
                    "student_id" => "0112231058",
                    "semester" => "7th trimester",
                    "university" => "United International University",
                    "image" => "photos/afia.jpg"
                ],
                [
                    "name" => "Monirul Islam",
                    "student_id" => "011222088",
                    "semester" => "8th trimester",
                    "university" => "United International University",
                    "image" => "photos/Moni.jpg"
                ]
            ];

            // Loop through the team members
            foreach ($team as $member) {
                echo "
                <div class='col-md-4'>
                    <div style='text-align: center; margin-bottom: 2rem;'>
                        <img src='{$member['image']}' 
                             alt='{$member['name']}' 
                             style='width: 100%; max-width: 300px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); margin-bottom: 1rem;'>
                        <h4 style='font-weight: bold; margin-bottom: 0.5rem; color: #333;'>{$member['name']}</h4>
                        <p style='margin-bottom: 0.2rem; font-size: 1rem; color: #777;'>ID: {$member['student_id']}</p>
                        <p style='margin-bottom: 0.2rem; font-size: 1rem; color: #777;'>Semester: {$member['semester']}</p>
                        <p style='margin-bottom: 0.2rem; font-size: 1rem; color: #777;'>University: {$member['university']}</p>
                    </div>
                </div>
                ";
            }
            ?>
        </div>
    </div>
</section>