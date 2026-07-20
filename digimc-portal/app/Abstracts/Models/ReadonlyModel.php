<?php

namespace App\Abstracts\Models;

use Illuminate\Database\Eloquent\Model;

class ReadonlyModel extends Model
{


    /**
     * @param array $options
     * @return bool
     * @throws \Exception
     */
    public function save(array $options = []): bool
    {
        if(config('env', 'local') == 'production')
        {
            throw new \Exception('This model is read-only');
        } else {
            return parent::save();
        }
    }

    /**
     * @param array $attributes
     * @param array $options
     * @return bool
     * @throws \Exception
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        if(config('env', 'local') == 'production') {
            throw new \Exception('This model is read-only');
        } else {
            return parent::update();
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function delete(): bool
    {
        if(config('env', 'local') == 'production') {
            throw new \Exception('This model is read-only');
        } else {
            return parent::delete();
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function forceDelete(): bool
    {
        if(config('env', 'local') == 'production')
        {
            throw new \Exception('This model is read-only');
        } else {
            return parent::forceDelete();
        }
    }
}
