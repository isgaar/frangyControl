<?php

namespace App\Policies;

use App\Models\Ordenes;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrdenPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ordenes $ordenes): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ordenes $ordenes): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ordenes $ordenes): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ordenes $ordenes): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // app/Policies/OrdenPolicy.php

    public function destroy(User $user, Ordenes $orden)
    {
        return $user->hasPermission('admin.orden.destroy') && $user->id === $orden->user_id;
    }

}