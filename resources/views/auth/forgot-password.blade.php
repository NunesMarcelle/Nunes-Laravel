<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Redefinir Senha</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>
        .login-container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            max-width: 900px;
            width: 100%;
        }
    </style>
</head>

<body class="bg-gradient-primary">

    <div class="container login-container">
        <div class="card o-hidden border-0 shadow-lg login-card">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <p class="mb-0">{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <div class="row no-gutters">

                <!-- Imagem -->
                <div class="col-md-7 d-none d-md-block" style="background-image: url('{{ asset('img/Tech.jpg') }}'); background-size: cover; background-position: center;"></div>

                <!-- Formulário -->
                <div class="col-md-5">
                    <div class="p-4">

                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Redefinir Senha</h1>
                        </div>

                        <form method="POST" action="{{ route('password.email') }}" class="user">
                            @csrf
                            <div class="form-group">
                                <input type="email" name="email" class="form-control form-control-user" placeholder="Digite seu e-mail" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                Enviar link de redefinição
                            </button>
                        </form>

                        <hr>

                        <div class="text-center">
                            <a class="small" href="{{ route('login') }}">Já tem uma conta? Faça login!</a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
