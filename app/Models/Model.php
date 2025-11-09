<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as LaravelModel;

class Model extends LaravelModel
{

    /**
	 * Overload model save.
	 */
    public function save (array $options = array(), $do_log = true)
    {

        $user_id = auth()->user()->id ?? null;
        if ($user_id==0) { // There are no login
            if (empty($this->id)) {
                if (array_key_exists('created_user', $this->attributes) && !empty($this->created_user)) {
                    $user_id = $this->created_user;
                }
            } else {
                if (array_key_exists('updated_user', $this->attributes) && !empty($this->updated_user)) {
                    $user_id = $this->updated_user;
                }
            }
        } else {
            if (empty($this->id)) {
                if (array_key_exists('created_user', $this->attributes)) {
                    // $this->company_id = auth()->user()->company_id;
                    $this->created_user = $user_id;
                }
            } else {
                if (array_key_exists('updated_user', $this->attributes)) {
                    $this->updated_user = $user_id;
                }
            }
        }
        if ($do_log) {
            LocalLog::create([
                'procedure' => get_class($this).'::save',
                'data' => [
                    'original' => $this->getOriginal(),
                    'changes' => $this->getDirty(),
                ],
                'created_user' => auth()->user()->id ?? null,
            ]);
        }
        parent::save($options); // Calls Default Save
    }
    /**
	 * Overload model delete.
	 */
    public function delete ($do_log = true)
    {
        if ($do_log) {
            LocalLog::create([
                'procedure' => get_class($this).'::delete',
                'data' => [
                    'id' => $this->id,
                ],
                'created_user' => auth()->user()->id ?? null,
            ]);
        }

        if (array_key_exists('updated_user', $this->attributes) && empty($this->updated_user)) {
            $this->updated_user = auth()->user()->id ?? $this->updated_user;
        }

        parent::delete(); // Calls Default Save
    }
}
