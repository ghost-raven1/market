<?php

namespace App\Services;

use App\Models\Advertisement;

class AdvertisementService
{
    public function createAdvertisement(array $data)
    {
        // Logic to create an advertisement
        return Advertisement::create($data);
    }

    public function updateAdvertisement(int $id, array $data)
    {
        // Logic to update an advertisement
        $advertisement = Advertisement::find($id);
        if ($advertisement) {
            $advertisement->update($data);
            return $advertisement;
        }
        return null;
    }

    public function deleteAdvertisement(int $id)
    {
        // Logic to delete an advertisement
        $advertisement = Advertisement::find($id);
        if ($advertisement) {
            $advertisement->delete();
            return true;
        }
        return false;
    }

    public function getAdvertisements()
    {
        // Logic to retrieve all advertisements
        return Advertisement::all();
    }
}
