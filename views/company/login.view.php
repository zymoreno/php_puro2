<!-- Start Contact -->
<div class="container py-5">
    <div class="row py-5">
        <div class="row text-center pt-3">
            <div class="col-lg-6 m-auto">
                <h1 class="h1">Inicio de Sesión</h1>
            </div>
        </div>

        <form class="col-md-9 m-auto" action="" method="post" aria-label="Formulario de inicio de sesión">
            <div class="mb-3">
                <label for="user_email">Usuario</label>
                <input
                    type="email"
                    class="form-control mt-1"
                    id="user_email"
                    name="user_email"
                    placeholder="Correo electrónico"
                    autocomplete="username"
                    required>
            </div>

            <div class="mb-3">
                <label for="user_pass">Contraseña</label>
                <input
                    type="password"
                    class="form-control mt-1"
                    id="user_pass"
                    name="user_pass"
                    placeholder="Entre 5 y 8 caracteres"
                    minlength="5"
                    maxlength="8"
                    autocomplete="current-password"
                    required>
            </div>

            <div class="row">
                <div class="col text-center mt-2">
                    <button type="submit" class="btn btn-success btn-lg px-3" aria-label="Enviar credenciales">
                        Enviar
                    </button>
                </div>
            </div>

            <div class="mb-3 mt-3">
                <h3 class="h5">
                    <?php echo isset($message) ? $message : ''; ?>
                </h3>
            </div>
        </form>
    </div>
</div>
<!-- End Contact -->

