<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card custom-card">
                        <div class="card-header custom-header">
                            Connexion
                        </div>
                        <div class="card-body custom-body">
                            <div class="login-icon">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            
                            @if ($errors->any())
                            <div class="alert alert-danger custom-alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            
                            @if (session('success'))
                            <div class="alert alert-success custom-alert">
                                {{ session('success') }}
                            </div>
                            @endif
                            
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="usr" class="form-label">Nom d'utilisateur</label>
                                    <div class="input-group">
                                        <span class="input-group-text custom-input-icon">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control custom-input" 
                                               id="usr" 
                                               name="usr" 
                                               value="{{ old('usr') }}" 
                                               required 
                                               autofocus>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="pwd" class="form-label">Mot de passe</label>
                                    <div class="input-group">
                                        <span class="input-group-text custom-input-icon">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" 
                                               class="form-control custom-input" 
                                               id="pwd" 
                                               name="pwd" 
                                               required>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary custom-btn">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Se connecter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>