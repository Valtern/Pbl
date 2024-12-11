<!DOCTYPE html>
<html lang="en">
<head>
    <title>Si Disiplin JTI</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/home.css">

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid border-bottom border-2">
        <a class="navbar-brand" href="#">
            <img src="img/brand1.png" alt="Logo" style="height: 30px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#"></a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <a href="index.php" class="btn btn-primary">Login</a>
            </div>
        </div>
    </div>
</nav>

    <div class="container mt-4">
        <div class="d-flex">
            <div class="nav flex-column nav-pills" role="tablist">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#home">Home page</button>
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#rules">Rules</button>
            </div>

            <div class="tab-content flex-grow-1">
                <div class="tab-pane fade show active" id="home">
                    <h1 class="welcome-title text-center">
                        Welcome to Si Disiplin JTI
                    </h1>
                    <h5>Si Disiplin JTI is a web-based system designed to support compliance with rules and regulations within the Information Technology Department. This system simplifies the process of recording, tracking, and notifying rule violations in real time to relevant parties, including students, lecturers, and department staff.</h5>
                    <h5>With advanced features for documenting and analyzing behavioral patterns, Si Disiplin JTI facilitates more effective decision-making in handling violations. Additionally, the system ensures transparency and speed in communication, creating an efficient solution for managing discipline.</h5>
                    <h5>In the future, Si Disiplin JTI will evolve into a tool that supports the automation of administrative actions, reducing the workload for department staff and lecturers. The system will analyze the severity of violations, provide actionable recommendations, and contribute to fostering a safe, conducive, and productive learning environment.</h5>
                </div>
                <div class="tab-pane fade" id="rules">
                    <div
                        class="table-responsive-sm"
                    >
                        <table
                            class="table table-light border border-1"
                        >
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Violation</th>
                                    <th scope="col">Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="">
                                    <td scope="row">Row 1 col 1</td>
                                    <td>Row 1 col 2</td>
                                    <td>Row 1 col 3</td>
                                </tr>
                                <tr class="">
                                    <td scope="row">R2C1</td>
                                    <td>R2C2</td>
                                    <td>R2C3</td>
                                </tr>
                                <tr class="">
                                    <td scope="row">R3C1</td>
                                    <td>R3C2</td>
                                    <td>R3C3</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.nav-pills .nav-link');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
        </script>
    </div>
</body>
</html>