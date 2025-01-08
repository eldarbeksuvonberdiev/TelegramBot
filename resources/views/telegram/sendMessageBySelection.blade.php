<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>

    <div class="container mt-3">
        <div class="row">
            <div class="col-12 mt-3">
                <h2>Telegram Bot With File</h2>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('danger'))
                    <div class="alert alert-danger">
                        {{ session('success') }}
                    </div>
                @endif
                <form action="{{ route('telegram.sendMessageBySelected') }}" method="post">
                    @csrf
                    <h3>Select Names:</h3>
                    <div class="form-check">
                        <label for="name1" class="form-check-label">Alice</label>
                        <input type="checkbox" id="name1" class="form-check-input" name="names[]" value="Alice">
                    </div>
                    <div class="form-check">
                        <label for="name2" class="form-check-label">Bob</label>
                        <input type="checkbox" id="name2" class="form-check-input" name="names[]" value="Bob">
                    </div>
                    <div class="form-check">
                        <label for="name3" class="form-check-label">Charlie</label>
                        <input type="checkbox" id="name3" class="form-check-input" name="names[]" value="Charlie">
                    </div>
                    <div class="form-check">
                        <label for="name4" class="form-check-label">Diana</label>
                        <input type="checkbox" id="name4" class="form-check-input" name="names[]" value="Diana">
                    </div>
                    <div class="form-check">
                        <label for="name5" class="form-check-label">Edward</label>
                        <input type="checkbox" id="name5" class="form-check-input" name="names[]" value="Edward">
                    </div>
                    <div class="form-check">
                        <label for="name6" class="form-check-label">Fiona</label>
                        <input type="checkbox" id="name6" class="form-check-input" name="names[]" value="Fiona">
                    </div>
                    <div class="form-check">
                        <label for="name7" class="form-check-label">George</label>
                        <input type="checkbox" id="name7" class="form-check-input" name="names[]" value="George">
                    </div>
                    <div class="form-check">
                        <label for="name8" class="form-check-label">Hannah</label>
                        <input type="checkbox" id="name8" class="form-check-input" name="names[]" value="Hannah">
                    </div>
                    <div class="form-check">
                        <label for="name9" class="form-check-label">Ivy</label>
                        <input type="checkbox" id="name9" class="form-check-input" name="names[]" value="Ivy">
                    </div>
                    <div class="form-check">
                        <label for="name10" class="form-check-label">Jack</label>
                        <input type="checkbox" id="name10" class="form-check-input" name="names[]" value="Jack">
                    </div>

                    <button class="btn btn-primary mt-3">Send</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
