<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Dashboadrd</h1>

    <form class="mt-4" action="{{ route('logout') }}" method="post">
        @csrf

        <div class="text-end"> <!-- Right-align the button -->
            <button type="submit" class="btn btn-primary">
                Logout
            </button>
        </div>

    </form>
</body>

</html>