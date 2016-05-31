<?php namespace App\Models;

use App\Models\Setting;
use Fenos\Notifynder\Notifable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'confirmed', 'approved',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * User-friendly model name.
     *
     * @return string
     */
    public $friendlyName = 'User';

    /**
     * Relationship: profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Relationship: socialite auths
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function auths()
    {
        return $this->hasMany(UserAuth::class);
    }

    /**
     * Relationship: comments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relationship: characters
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function characters()
    {
        return $this->hasMany(Character::class);
    }

    /**
     * Relationship: roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Scope: confirmed
     *
     * @param  \Illuminate\Database\Query\Builder
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeConfirmed($query)
    {
        return $query->where('confirmed', 1);
    }

    /**
     * Scope: approved
     *
     * @param  \Illuminate\Database\Query\Builder
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', 1);
    }

    /**
     * Scope: active (approved + confirmed)
     *
     * @param  \Illuminate\Database\Query\Builder
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeActive($query)
    {
        return $query->where(['confirmed' => 1, 'approved' => 1]);
    }

    /**
     * Attribute: is active (both confirmed and approved)
     *
     * @return string
     */
    public function getIsActiveAttribute()
    {
        return $this->confirmed && $this->approved;
    }

    /**
     * Attribute: display name
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        if (!is_null($this->profile->family_name)) {
            return "{$this->name} ({$this->profile->family_name})";
        }

        return $this->name;
    }

    /**
     * Attribute: role list
     *
     * @return string
     */
    public function getRoleListAttribute()
    {
        return implode(', ', $this->roles()->lists('name')->toArray());
    }

    /**
     * Attribute: main character
     *
     * @return string
     */
    public function getMainCharacterAttribute()
    {
        return $this->characters()->where('main', 1)->first();
    }

    /**
     * Attribute: slugified name
     *
     * @return string
     */
    public function getSlugAttribute()
    {
        return str_slug($this->name, '-');
    }

    /**
     * Attribute: profile URL
     *
     * @return string
     */
    public function getProfileUrlAttribute()
    {
        return url("user/{$this->id}-{$this->slug}");
    }

    /**
     * Helper: determine if the user has a given role (or one of multiple given roles)
     *
     * @param  string|array  $role
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            foreach ($role as $r) {
                if ($this->hasRole($r)) return true;
            }

            return false;
        }

        foreach ($this->roles as $r) {
            if ($r->name == $role) return true;
        }

        return false;
    }

    /**
     * Helper: confirm the user if applicable
     *
     * @return void
     */
    public function confirm()
    {
        if ($this->confirmed) {
            return;
        }

        $this->confirmed = 1;
        $this->save();
    }
}
