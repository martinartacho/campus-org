<form method="POST">
    @csrf

    <h2>Dades bàsiques</h2>
    <input name="name" value="{{ old('name', $teacher->name) }}" required>
    <input name="email" type="email" value="{{ old('email', $teacher->email) }}" required>

    <hr>

    <label>
        <input type="checkbox" name="consent_rgpd" required>
        Accepto el tractament de dades (RGPD)
    </label>

    <hr>

    <label>
        <input type="checkbox" name="needs_payment" value="1">
        Rebo pagaments
    </label>

    <div>
        <input name="first_name" placeholder="Nom">
        <input name="last_name_1" placeholder="Cognom 1">
        <input name="last_name_2" placeholder="Cognom 2">
        <input name="dni" placeholder="DNI / NIE">
        <input name="postal_code" placeholder="Codi postal">
        <input name="iban" placeholder="IBAN">
        <input name="bank_holder" placeholder="Titular del compte">
    </div>

    <button>Enviar</button>
</form>
