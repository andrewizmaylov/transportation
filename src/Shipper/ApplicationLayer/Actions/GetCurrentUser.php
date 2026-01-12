<?php

declare(strict_types=1);

namespace Src\Shipper\ApplicationLayer\Actions;

use App\Exceptions\BusinessException;
use Src\SharedKernel\DomainLayer\Entities\UserEntity;
use Src\SharedKernel\DomainLayer\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

readonly class GetCurrentUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    /**
     * @throws BusinessException
     */
    public function execute(): UserEntity
    {
        $user = $this->userRepository->getCurrentUser();

        if (! $user) {
            throw new BusinessException('User is not logged in', Response::HTTP_UNAUTHORIZED);
        }

        if (! $user->isShipper) {
            throw new BusinessException('User is not Shipper', Response::HTTP_CONFLICT);
        }

        return $user;
    }
}
