<?php

declare(strict_types=1);

namespace Src\Transportation\InfrastructureLayer\Repository;

use App\Models\Transportation as TransportationModel;
use Illuminate\Database\ConnectionInterface;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\SharedKernel\DomainLayer\ValueObjects\PaginatedResult;
use Src\Transportation\DomainLayer\Entities\Transportation;
use Src\Transportation\DomainLayer\Repository\TransportationRepositoryInterface;
use Src\Transportation\PresentationLayer\HTTP\V1\Responder\TransportationResponder;

class TransportationRepository implements TransportationRepositoryInterface
{
    protected string $tableName;

    public function __construct(
        protected ConnectionInterface $connection,
        protected TransportationResponder $responder,
    ) {
        $this->tableName = TransportationModel::getTableName();
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function findById(TransportationId $id): ?Transportation
    {
        $record = $this->connection
            ->table($this->tableName)
            ->whereNull('deleted_at')
            ->where('id', '=', $id->value())
            ->first();

        if (! $record) {
            return null;
        }

        return $this->responder->composeFromModel($record);
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function getAllWithPagination(int $page = PaginatedResult::CURRENT_PAGE, int $perPage = PaginatedResult::PER_PAGE, ?array $filter = null): PaginatedResult
    {
        $builder = $this->connection
            ->table($this->tableName)
            ->orderByDesc(TransportationModel::CREATED_AT);

        // If deleted records need to be displayed, the flag is set to TRUE,
        // otherwise - FALSE.
        if (isset($filter['withTrashed']) && $filter['withTrashed'] === 'false') {
            $builder->whereNull('deleted_at');
        }

        if (isset($filter['clientId'])) {
            $builder->where('client_id', $filter['clientId']);
        }

        $totalRecords = $builder->count();

        $list = $builder->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get()
            ->toArray();

        $list = array_map(fn ($record) => $this->responder->composeFromModel($record), $list);

        return new PaginatedResult(
            items: $list,
            currentPage: $page,
            lastPage: (int) ceil($totalRecords / $perPage),
            perPage: $perPage,
            totalRecords: $totalRecords,
        );
    }
}
