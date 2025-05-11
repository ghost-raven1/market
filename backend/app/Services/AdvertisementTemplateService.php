<?php

namespace App\Services;

use App\Models\AdvertisementTemplate;

class AdvertisementTemplateService
{
    public function createTemplate(array $data)
    {
        // Logic to create an advertisement template
        return AdvertisementTemplate::create($data);
    }

    public function updateTemplate(int $id, array $data)
    {
        // Logic to update an advertisement template
        $template = AdvertisementTemplate::find($id);
        if ($template) {
            $template->update($data);
            return $template;
        }
        return null;
    }

    public function deleteTemplate(int $id)
    {
        // Logic to delete an advertisement template
        $template = AdvertisementTemplate::find($id);
        if ($template) {
            $template->delete();
            return true;
        }
        return false;
    }

    public function getTemplates()
    {
        // Logic to retrieve all advertisement templates
        return AdvertisementTemplate::all();
    }
}
