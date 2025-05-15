<?php 
// Verificar se a sessão já foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="side-bar">
    <div class="user-p">
        <img src="img/user.png" alt="Imagem de usuário">
        <h4>@<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Usuário não logado'; ?></h4>
    </div>

    <?php 
    // Verifica se o usuário tem o papel 'employee'
    if (isset($_SESSION['role']) && $_SESSION['role'] == "employee") {
    ?>
        <!-- Navegação para Funcionários -->
        <ul id="navList">
            <li><a href="index.php"><i class="fa fa-tachometer" aria-hidden="true"></i><span>Painel</span></a></li>
            <li><a href="my_task.php"><i class="fa fa-tasks" aria-hidden="true"></i><span>Novo Equipamento</span></a></li>
            <li><a href="consulta.php"><i class="fa fa-search" aria-hidden="true"></i><span>Consultas</span></a></li>
            <li><a href="cadastro_anydesk.php"><i class="fa fa-laptop" aria-hidden="true"></i><span>Anydesk</span></a></li>
            <li><a href="nova_os.php"><i class="fa fa-cogs" aria-hidden="true"></i><span>Ordem de Serviço</span></a></li>
            <li><a href="chamados.php"><i class="fa fa-phone-square" aria-hidden="true"></i><span>Chamados</span></a></li>
            <li><a href="my_task.php"><i class="fa fa-exchange" aria-hidden="true"></i><span>Entradas e Saídas</span></a></li>
            <li><a href="backup.php"><i class="fa fa-database" aria-hidden="true"></i><span>Backup</span></a></li>
            <li><a href="User.php"><i class="fa fa-user" aria-hidden="true"></i><span>Perfil</span></a></li>
            <li><a href="logs.php"><i class="fa fa-bell" aria-hidden="true"></i><span>Logs</span></a></li>
            <li><a href="cadastro_usuario.php"><i class="fa fa-id-card" aria-hidden="true"></i><span>Cadastro Usuários</span></a></li>
            <li><a href="notifications.php"><i class="fa fa-bell" aria-hidden="true"></i><span>Notificações</span></a></li>
            <li><a href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i><span>Saída</span></a></li>
        </ul>
    <?php 
    // Se o usuário tem o papel 'admin'
    } elseif (isset($_SESSION['role']) && $_SESSION['role'] == "admin") {
    ?>
        <!-- Navegação para Administradores -->
        <ul id="navList">
            <li><a href="index.php"><i class="fa fa-tachometer" aria-hidden="true"></i><span>Painel</span></a></li>
            <li><a href="user.php"><i class="fa fa-users" aria-hidden="true"></i><span>Usuários</span></a></li>
            <li><a href="create_task.php"><i class="fa fa-plus" aria-hidden="true"></i><span>Abrir Chamado</span></a></li>
            <li><a href="tasks.php"><i class="fa fa-list" aria-hidden="true"></i><span>Todos os Chamados</span></a></li>
            <li><a href="settings.php"><i class="fa fa-cogs" aria-hidden="true"></i><span>Configurações</span></a></li>
            <li><a href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i><span>Saída</span></a></li>
        </ul>
    <?php } else { ?>
        <!-- Se o usuário não estiver logado ou não tiver papel definido -->
        <p><a href="login.php">Faça o login</a></p>
    <?php } ?>
</nav>