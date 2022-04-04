<?php
include_once "conexao.php";
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Celke - Formulario de Contato</title>
</head>

<body>
    <h2>Formulário de Contato</h2>

    <?php
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (!empty($dados['SendCadMsg'])) {
        //var_dump($dados);
        $query_msg = "INSERT INTO mensagens (nome_usuario, email_usuario, assunto, mensagem) VALUES (:nome_usuario, :email_usuario, :assunto, :mensagem)";
        $cad_msg = $conn->prepare($query_msg);
        $cad_msg->bindParam(':nome_usuario', $dados['nome_usuario'], PDO::PARAM_STR);
        $cad_msg->bindParam(':email_usuario', $dados['email_usuario'], PDO::PARAM_STR);
        $cad_msg->bindParam(':assunto', $dados['assunto'], PDO::PARAM_STR);
        $cad_msg->bindParam(':mensagem', $dados['mensagem']);

        $cad_msg->execute();

        if ($cad_msg->rowCount()) {
            require 'lib/vendor/autoload.php';

            $email = new \SendGrid\Mail\Mail();

            $email->setFrom("cesar@celke.com.br", "Cesar");
            $email->setSubject("Mensagem recebida com sucesso!");
            $email->addTo($dados['email_usuario'], $dados['nome_usuario']);
            $email->addContent("text/plain", "Recebi a mensagem, em breve será respondida: " . $dados['mensagem']);
            $email->addContent(
                "text/html",
                "Recebi a mensagem, em breve será respondida: " . $dados['mensagem']
            );

            $sendgrid = new \SendGrid('SENDGRID_API_KEY');

            try {
                $response = $sendgrid->send($email);
                echo "Mensagem enviada com sucesso!<br>";
            } catch (Exception $e) {
                //echo 'Caught exception: ' . $e->getMessage() . "\n";
                echo "Erro: Mensagem não enviada com sucesso!<br>";
            }
        } else {
            echo "Erro: Mensagem não enviada com sucesso!<br>";
        }
    }
    ?>

    <form method="POST" action="">
        <label>Nome: </label>
        <input type="text" name="nome_usuario" placeholder="Nome completo"><br><br>

        <label>E-mail: </label>
        <input type="email" name="email_usuario" placeholder="Seu melhor e-mail"><br><br>

        <label>Assunto: </label>
        <input type="text" name="assunto" placeholder="Assunto da mensagem"><br><br>

        <label>Mensagem: </label>
        <textarea name="mensagem" rows="5" cols="30"></textarea><br><br>

        <input type="submit" value="Enviar" name="SendCadMsg">

    </form>
</body>

</html>