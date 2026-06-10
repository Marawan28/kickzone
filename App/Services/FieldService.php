<?php

namespace App\Services;

use App\Models\Field;

class FieldService
{
    // دالة لجلب كل الملاعب مع الصور والتقييمات
    public function getAllFields(array $filters = [])
    {
        $query = Field::with(['images', 'city']);

        if (isset($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        return $query->get();
    }

    // دالة لجلب تفاصيل ملعب واحد
    public function getFieldDetails($id)
    {
        return Field::with(['images', 'reviews.user', 'slots'])->findOrFail($id);
    }

    public function createField(array $data)
    {
        // Create field
        $field = Field::create([
            'name'           => $data['name'],
            'address'        => $data['address'],
            'location'       => $data['location'] ?? null,
            'price_per_hour' => $data['price_per_hour'],
            'city_id'        => $data['city_id'],
            'description'    => $data['description'] ?? null,
            'owner_id'       => auth()->id(),
        ]);

        // Create default slots for the field.
        // field_slots migration uses: start_time, is_booked
        $hours = ['04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00'];
        foreach ($hours as $hour) {
            $field->slots()->create([
                'field_id'   => $field->id,
                'start_time' => $hour,
                'is_booked'   => false,
                // end_time is nullable in some DB setups; if required, adjust migration.
            ]);
        }

        return $field;
    }

    public function getAvailableSlots($fieldId)
    {
        // حالياً هنعمل مصفوفة مواعيد ثابتة للتجربة
        // قدام شوية هنخليها تتجاب من الداتابيز بناءً على الحجوزات
        return [
            ['time' => '04:00 PM', 'is_available' => true],
            ['time' => '05:00 PM', 'is_available' => true],
            ['time' => '06:00 PM', 'is_available' => true],
            ['time' => '07:00 PM', 'is_available' => true],
            ['time' => '08:00 PM', 'is_available' => true],
            ['time' => '09:00 PM', 'is_available' => true],
            ['time' => '10:00 PM', 'is_available' => true],
            ['time' => '11:00 PM', 'is_available' => true],
            ['time' => '12:00 PM', 'is_available' => true],
        ];
    }
}

