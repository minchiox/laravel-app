<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ProfileController extends Controller
{
    private const AVATAR_DISK = 'local';

    private const AVATAR_DIR = 'avatars';

    //create controller instance
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        return Inertia::render('profile', [
            // Prop dedicata invece di gonfiare auth.user/AuthUser condiviso
            // con campi (phone, city) che servono solo a questa pagina.
            'profileUser' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'city' => $user->city,
                'avatarUrl' => $user->avatar ? route('user.avatar', $user->avatar) : null,
            ],
        ]);
    }


    //change value in profile page validation
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // 'required' da solo accettava qualunque stringa: si poteva
            // salvare un'email non valida o quella di un altro utente, con
            // cui poi non si rientrava piu' (l'email e' l'identificativo di
            // login).
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore(auth()->id())],
            // Prima non c'era alcun vincolo sulla password nuova, e nessuna
            // richiesta della password attuale: una sessione rubata bastava
            // a cambiare le credenziali senza saperle.
            'current_password' => ['required_with:password', 'current_password'],
            'password' => ['nullable', Password::min(8)],
            'confirm_password' => 'required_with:password|same:password',
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:255'],
            // `image` valida solo il contenuto del file, non basta da sola:
            // `mimes` restringe ai soli formati raster comuni ed esclude
            // deliberatamente svg (vettore di XSS via <script> nell'SVG).
            'avatar' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // $request->all() lasciava passare qualunque campo presente nel POST:
        // con isTeacher fra i $fillable, uno studente si promuoveva docente
        // aggiungendo un input al form. Qui si accetta solo cio' che il profilo
        // puo' davvero modificare.
        $input = $request->only(['name', 'email', 'phone', 'city']);

        if ($request->hasFile('avatar')) {
            $input['avatar'] = $this->storeAvatar($request->file('avatar'));
        }

        if ($request->filled('password')) {
            $input['password'] = Hash::make($request->input('password'));
        }

        auth()->user()->update($input);

        return back()->with('success', 'Profilo aggiornato con successo.');
    }

    /**
     * Salva l'avatar fuori dalla webroot (storage/app, non public/) con un
     * nome generato dal framework a partire dal contenuto reale del file, mai
     * dall'estensione dichiarata dal client. `getClientOriginalExtension()`
     * si fida di una stringa scelta dall'attaccante: la validazione blocca
     * solo le estensioni note come .php/.phtml/.phar, non tutte le altre, e
     * su Laravel 10.33 CVE-2025-27515 mostra che anche quel controllo e'
     * aggirabile. Il file finiva comunque sotto public/, l'unica cartella che
     * nginx esegue come PHP.
     */
    private function storeAvatar(UploadedFile $avatar): string
    {
        $avatarName = $avatar->hashName();

        $avatar->storeAs(self::AVATAR_DIR, $avatarName, self::AVATAR_DISK);

        return $avatarName;
    }

    /**
     * Serve l'avatar da storage/app: qui, e non piu' come file statico sotto
     * public/, cosi' un upload malevolo resta comunque irraggiungibile via
     * HTTP anche se un giorno la validazione venisse allentata per errore.
     */
    public function avatar(string $filename)
    {
        $path = self::AVATAR_DIR.'/'.$filename;

        abort_unless(Storage::disk(self::AVATAR_DISK)->exists($path), 404);

        return Storage::disk(self::AVATAR_DISK)->response($path);
    }

}
