<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use App\Entity\Vehicle;
use App\Entity\Car;
use App\Entity\Motorcycle;
use App\Entity\Van;
use App\Entity\User;

final class VehicleVoter extends Voter
{
    public const CREATE = 'VEHICLE_CREATE';
    public const EDIT = 'VEHICLE_EDIT';
    public const VIEW = 'VEHICLE_VIEW';
    public const MANAGE_ALL = 'VEHICLE_MANAGE_ALL';
    protected function supports(string $attribute, mixed $subject): bool
    {

        return in_array($attribute, [
            self::CREATE,
            self::EDIT, 
            self::VIEW,
            self::MANAGE_ALL 

        ]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $vehicle = $subject;

        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($user);
            case self::EDIT:
                return $this->canEdit($user, $subject);
            case self::VIEW:
                return $this->canView();
            case self::MANAGE_ALL:
                return $this->canManageAll($user);
            default:
                return false;
        }
    }

    private function canCreate(User $user): bool
    {     
        if(in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }
        return false ;
    }

    private function canEdit(User $user, Car|Van|Motorcycle $vehicle): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canView(): bool
    {
        return true;
    }

    private function canManageAll(User $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

}
