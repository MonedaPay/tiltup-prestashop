<p>Payment confirmed</p>
<

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link href="{$tu_base_dir}assets/presta-ui-kit/bootstrap-prestashop-ui-kit.css" rel="stylesheet">
    <script src="{$tu_base_dir}assets/presta-ui-kit/prestashop-ui-kit.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#520CE9',
                    },
                },
            },
        };
    </script>
    <style>
        .tu_button {
            background-color: #520CE9;
            border-radius: 0.5rem;
            padding: 0.625rem 1.25rem;
            margin: 0.5rem;
            color: white;
            font-weight: 500;
        }
    </style>

    <title>TiltUp success</title>
</head>
<body>
<div class='flex flex-col'>
    <p>Payment confirmed</p>
    <div>
        <button class="tu_button">fafafa</button>
        <img class="img-thumbnail" src="{$tu_base_dir}assets/graphics/payment-success.svg" alt="payment success"/>
    </div>


</div>

<!-- the below should look like it's coming from presta -->
<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Username</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th scope="row">1</th>
        <td>Mark</td>
        <td class="active">Otto</td>
        <td>@mdo</td>
    </tr>
    <tr>
        <th scope="row">2</th>
        <td>Jacob</td>
        <td>Thornton</td>
        <td>@fat</td>
    </tr>
    <tr>
        <th scope="row">3</th>
        <td>Larry</td>
        <td>the Bird</td>
        <td>@twitter</td>
    </tr>
    </tbody>
</table>
</body>
</html>
