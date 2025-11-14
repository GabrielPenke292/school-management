<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Gera o username automaticamente no formato primeiro.ultimo
     * Exemplo: "Carlos da Silva Santos" -> "carlos.santos"
     * 
     * @param string $name Nome completo do usuário
     * @return string Username gerado
     */
    public static function generateUsername(string $name): string
    {
        // Remove espaços extras e divide o nome em partes
        $parts = array_filter(explode(' ', trim($name)));
        
        if (empty($parts)) {
            return strtolower(str_replace(' ', '.', $name));
        }

        // Pega o primeiro nome
        $firstName = strtolower($parts[0]);
        
        // Pega o último nome (última parte do array)
        $lastName = strtolower(end($parts));
        
        // Remove acentos e caracteres especiais
        $firstName = self::removeAccents($firstName);
        $lastName = self::removeAccents($lastName);
        
        // Remove caracteres especiais, mantendo apenas letras
        $firstName = preg_replace('/[^a-z]/', '', $firstName);
        $lastName = preg_replace('/[^a-z]/', '', $lastName);
        
        return $firstName . '.' . $lastName;
    }

    /**
     * Remove acentos de uma string
     * 
     * @param string $string
     * @return string
     */
    private static function removeAccents(string $string): string
    {
        $accents = [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
            'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C', 'Ñ' => 'N'
        ];
        
        return strtr($string, $accents);
    }

    /**
     * Boot do modelo - gera username automaticamente se não fornecido
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Se o username não foi fornecido e não é admin, gera automaticamente
            if (empty($user->username) && strtolower($user->name) !== 'admin') {
                $baseUsername = self::generateUsername($user->name);
                $username = $baseUsername;
                $counter = 1;
                
                // Verifica se o username já existe e adiciona um número se necessário
                while (self::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }
                
                $user->username = $username;
            }
        });
    }
}
