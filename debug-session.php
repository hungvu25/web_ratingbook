<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Session & Icon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Debug Session & Icon Status</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Session Data</h5>
                    </div>
                    <div class="card-body">
                        <pre><?php print_r($_SESSION); ?></pre>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Icon Test</h5>
                    </div>
                    <div class="card-body">
                        <h6>Font Awesome Icons:</h6>
                        <div style="font-size: 24px;">
                            <i class="fas fa-book-reader"></i> fa-book-reader<br>
                            <i class="fas fa-home"></i> fa-home<br>
                            <i class="fas fa-star"></i> fa-star<br>
                            <i class="fas fa-user"></i> fa-user<br>
                            <i class="fas fa-cog"></i> fa-cog<br>
                        </div>
                        
                        <h6 class="mt-3">Expected Display:</h6>
                        <p>Icons should show as Font Awesome icons or emoji fallbacks, not bullets or missing.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5>Font Test</h5>
            </div>
            <div class="card-body">
                <h2 style="font-family: 'Roboto', sans-serif;">Roboto Font Test</h2>
                <p>Current computed font: <span id="currentFont">Loading...</span></p>
                <p>Font should be: Roboto or system fallback</p>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('currentFont').textContent = getComputedStyle(document.body).fontFamily;
    </script>
</body>
</html> 