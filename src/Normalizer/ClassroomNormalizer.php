<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Model\Classroom\Entity\Classroom;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class ClassroomNormalizer implements ContextAwareNormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        /** @var Classroom $object */
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'is_active' => $object->isActive(),
            'created_at' => $object->getCreatedAt()->format(Classroom::DEFAULT_DATETIME_FORMAT),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Classroom;
    }
}
