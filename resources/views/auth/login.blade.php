<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        /* Reset et base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Container principal */
        .login-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }

        /* Card styles */
        .custom-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            border: none;
        }

        .custom-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            border-bottom: none;
        }

        .custom-body {
            padding: 30px;
        }

        /* Icon de connexion */
        .login-icon {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-icon i {
            font-size: 80px;
            color: #667eea;
        }

        /* Formulaire styles */
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .custom-input-icon {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            color: #667eea;
            padding: 12px 15px;
            border-radius: 8px 0 0 8px;
        }

        .custom-input {
            border: 2px solid #e9ecef;
            border-left: none;
            border-radius: 0 8px 8px 0;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .custom-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .custom-input:focus + .custom-input-icon,
        .input-group:focus-within .custom-input-icon {
            border-color: #667eea;
        }

        /* Bouton personnalisé */
        .custom-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .custom-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .custom-btn:active {
            transform: translateY(0);
        }

        /* Alertes personnalisées */
        .custom-alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border: none;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .alert li {
            margin-bottom: 5px;
        }

        .alert li:last-child {
            margin-bottom: 0;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .mb-3 {
            margin-bottom: 20px;
        }

        .d-grid {
            display: grid;
        }

        .me-2 {
            margin-right: 8px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                padding: 15px;
            }

            .custom-body {
                padding: 20px;
            }

            .login-icon i {
                font-size: 60px;
            }

            .custom-header {
                font-size: 20px;
                padding: 15px;
            }
        }

        /* Icons avec pseudo-elements pour remplacer Font Awesome */
        .fas {
            display: inline-block;
            font-style: normal;
            font-variant: normal;
            text-rendering: auto;
            line-height: 1;
        }

        .fa-user-circle::before {
            content: "👤";
            font-size: 1em;
        }

        .fa-user::before {
            content: "👤";
            font-size: 0.9em;
        }

        .fa-lock::before {
            content: "🔒";
            font-size: 0.9em;
        }

        .fa-sign-in-alt::before {
            content: "🚪";
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="custom-card">
            <div class="custom-header">
                Connexion
            </div>
            <div class="custom-body">
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
                            <span class="custom-input-icon">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" 
                                   class="custom-input" 
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
                            <span class="custom-input-icon">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" 
                                   class="custom-input" 
                                   id="pwd" 
                                   name="pwd" 
                                   required>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="custom-btn">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Se connecter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>