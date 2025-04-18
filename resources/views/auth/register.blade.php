<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Cadastro</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <!-- Custom styles -->
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
            <div class="row no-gutters">

                <!-- Imagem -->
                <div class="col-md-7 d-none d-md-block" style="background-image: url('{{ asset('img/Tech.jpg') }}'); background-size: cover; background-position: center;"></div>

                <!-- Formulário -->
                <div class="col-md-5">
                    <div class="p-4">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Criar Conta</h1>
                        </div>
                        <form method="POST" action="{{ route('register') }}" class="user" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <input type="text" name="name" class="form-control form-control-user" placeholder="Nome Completo" required>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" class="form-control form-control-user" placeholder="Digite seu e-mail" required>
                            </div>

                            <div class="form-group">
                                <input type="text" name="company_name" class="form-control form-control-user" placeholder="Nome da empresa" required>
                            </div>

                            <div class="form-group">
                                <input type="password" name="password" class="form-control form-control-user" placeholder="Senha" required>
                            </div>
                            <div class="form-group">
                                <input type="password" name="password_confirmation" class="form-control form-control-user" placeholder="Confirmar Senha" required>
                            </div>

                            <div class="form-group">
                                <label for="image">Foto de perfil</label>
                                <input type="file" name="img" id="image" class="form-control-file" accept="image/*">
                            </div>

                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                Registrar
                            </button>
                        </form>
                        <hr>

                        <div class="text-center">
                            <a class="small" href="{{ url('/login') }}">Já tenho conta</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
