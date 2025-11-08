<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AD | Absensi</title>

    {{-- bootstrap css --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">

    <!-- logo tab -->
    <link rel="icon" type="image/png" href="public/img/logo-ad.png">

    <!-- fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        onload="this.media='all'" />

    <!-- bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />

    <style>
        .btn {
            transition: background-color 0.2s ease;
        }

        .btn:hover {
            background-color: #dff7ffff !important;
            /* abu-abu saat hover */
        }
    </style>

</head>

<body class="bg-light">

    <div class="d-flex">

        <div class="container">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-4">
                    <div class="card my-5">
                        <form action="{{ route('login.submit') }}" method="POST"
                            class="card-body cardbody-color p-lg-5"> @csrf

                            {{-- logo --}}
                            <div class="text-center mb-5">
                                <img src="public/img/logo-ad.png" class="img-fluid my-3" width="100px">
                                <h2>Akar Daya</h2>
                            </div>

                            {{-- username input --}}
                            <div class="mb-3">
                                <input type="text" name="username" class="form-control" id="Username"
                                    placeholder="Username" required autofocus>
                            </div>

                            {{-- password input --}}
                            <div class="mb-3 position-relative">
                                <input type="password" name="password" class="form-control" id="password"
                                    placeholder="Password" aria-label="Password" required>
                            </div>

                            {{-- submit button --}}
                            <div class="text-center"><button type="submit"
                                    class="btn btn-color px-5 mb-5 w-100">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
