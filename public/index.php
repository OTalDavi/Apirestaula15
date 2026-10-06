<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$usuarios = [
    [
        'id' => 1,
        'nome' => 'Davi',
        'email' => 'davi@email.com',
        'login' => 'davi',
        'senha' => '123456'
    ],
    [
        'id' => 2,
        'nome' => 'João',
        'email' => 'joao@email.com',
        'login' => 'joao',
        'senha' => '123456'
    ],
    [
        'id' => 3,
        'nome' => 'Maria',
        'email' => 'maria@email.com',
        'login' => 'maria',
        'senha' => '123456'
    ],
    [
        'id' => 4,
        'nome' => 'Pedro',
        'email' => 'pedro@email.com',
        'login' => 'pedro',
        'senha' => '123456'
    ],
    [
        'id' => 5,
        'nome' => 'Ana',
        'email' => 'ana@email.com',
        'login' => 'ana',
        'senha' => '123456'
    ]
];

$app->get('/status', function ($request, $response) {

    $response->getBody()->write(
        json_encode(['status' => 'ok'])
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});


$app->get('/usuarios', function ($request, $response) use (&$usuarios) {

    $response->getBody()->write(
        json_encode($usuarios)
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});


$app->get('/usuarios/{id}', function ($request, $response, $args) use (&$usuarios) {

    $id = (int) $args['id'];

    foreach ($usuarios as $usuario) {

        if ($usuario['id'] === $id) {

            $response->getBody()->write(
                json_encode($usuario)
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }
    }

    $response->getBody()->write(
        json_encode(['erro' => 'Usuário não encontrado'])
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
});


$app->post('/usuarios', function ($request, $response) use (&$usuarios) {

    $dados = $request->getParsedBody();

    if (
        !isset($dados['nome']) ||
        !isset($dados['email']) ||
        !isset($dados['login']) ||
        !isset($dados['senha'])
    ) {

        $response->getBody()->write(
            json_encode(['erro' => 'Todos os campos são obrigatórios'])
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }

    $novoUsuario = [
        'id' => count($usuarios) + 1,
        'nome' => $dados['nome'],
        'email' => $dados['email'],
        'login' => $dados['login'],
        'senha' => $dados['senha']
    ];

    $usuarios[] = $novoUsuario;

    $response->getBody()->write(
        json_encode($novoUsuario)
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(201);
});


$app->put('/usuarios/{id}', function ($request, $response, $args) use (&$usuarios) {

    $id = (int) $args['id'];

    $dados = $request->getParsedBody();

    foreach ($usuarios as &$usuario) {

        if ($usuario['id'] === $id) {

            if (isset($dados['nome']) && !empty($dados['nome'])) {
                $usuario['nome'] = $dados['nome'];
            }

            if (isset($dados['email']) && !empty($dados['email'])) {
                $usuario['email'] = $dados['email'];
            }

            if (isset($dados['login']) && !empty($dados['login'])) {
                $usuario['login'] = $dados['login'];
            }

            if (isset($dados['senha']) && !empty($dados['senha'])) {
                $usuario['senha'] = $dados['senha'];
            }

            $response->getBody()->write(
                json_encode($usuario)
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }
    }

    $response->getBody()->write(
        json_encode(['erro' => 'Usuário não encontrado'])
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
});


$app->delete('/usuarios/{id}', function ($request, $response, $args) use (&$usuarios) {

    $id = (int) $args['id'];

    foreach ($usuarios as $indice => $usuario) {

        if ($usuario['id'] === $id) {

            unset($usuarios[$indice]);

            return $response->withStatus(204);
        }
    }

    $response->getBody()->write(
        json_encode(['erro' => 'Usuário não encontrado'])
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
});


$app->post('/login', function ($request, $response) use (&$usuarios) {

    $dados = $request->getParsedBody();

    if (
        !isset($dados['login']) ||
        !isset($dados['senha'])
    ) {

        $response->getBody()->write(
            json_encode(['erro' => 'Login e senha são obrigatórios'])
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }

    foreach ($usuarios as $usuario) {

        if (
            $usuario['login'] === $dados['login'] &&
            $usuario['senha'] === $dados['senha']
        ) {

            $response->getBody()->write(
                json_encode([
                    'mensagem' => 'Login realizado com sucesso',
                    'usuario' => $usuario['nome']
                ])
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }
    }

    $response->getBody()->write(
        json_encode(['erro' => 'Login ou senha inválidos'])
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(401);
});


$app->put('/usuarios/{id}/senha', function ($request, $response, $args) use (&$usuarios) {

    $id = (int) $args['id'];

    $dados = $request->getParsedBody();

    if (!isset($dados['senha']) || empty($dados['senha'])) {

        $response->getBody()->write(
            json_encode(['erro' => 'A nova senha é obrigatória'])
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }

    foreach ($usuarios as &$usuario) {

        if ($usuario['id'] === $id) {

            $usuario['senha'] = $dados['senha'];

            $response->getBody()->write(
                json_encode([
                    'mensagem' => 'Senha alterada com sucesso'
                ])
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }
    }

    $response->getBody()->write(
        json_encode(['erro' => 'Usuário não encontrado'])
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
});


$app->run();