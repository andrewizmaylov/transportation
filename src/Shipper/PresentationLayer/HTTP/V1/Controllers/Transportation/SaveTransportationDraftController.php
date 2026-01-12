<?php

declare(strict_types=1);

namespace Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation;

use App\Exceptions\BusinessException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Psr\Log\LoggerInterface;
use Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Cargo\AddCargoToTransportationController;
use Src\Shipper\PresentationLayer\HTTP\V1\Requests\AddCargoToTransportationRequest;
use Src\Shipper\PresentationLayer\HTTP\V1\Requests\AddNewTransportationAddressRequest;
use Src\Shipper\PresentationLayer\HTTP\V1\Requests\RegisterTransportationRequest;
use Symfony\Component\HttpFoundation\Response;

class SaveTransportationDraftController
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws BusinessException
     */
    public function __invoke(string $draftId, Request $request)
    {

        $step = $request->step;
        $data = $request->input('data', []);

        // Determine which Form Request class to use based on step
        $formRequestClass = match (true) {
            $step === 'transportationStep' => RegisterTransportationRequest::class,
            in_array($step, ['pickupAddressStep', 'deliveryAddressStep']) => AddNewTransportationAddressRequest::class,
            $step === 'cargoStep' => AddCargoToTransportationRequest::class,
//            $step === 'confirmStep' => ConfirmTransportationRequest::class,
            default => throw new BusinessException('Unknown transportation step'),
        };

        // Create mock request with the data
        // Some controllers need route parameters (like transportation_id), so we need to handle that
        $mockRequest = $this->createMockRequest($step, $data, $draftId);

        try {
            // Validate using the Form Request's rules
            $this->validateWithFormRequest($formRequestClass, $mockRequest);

            // If we get here, validation passed
            $userID = auth()->id();
            $previousData = Cache::get("{$userID}_transportation_draft_{$draftId}");
            $this->logger->debug('Draft saved', [
                'previous' => $previousData,
                'current' => $data,
            ]);

            Cache::put("{$userID}_transportation_draft_{$draftId}", [
                'step' => $request->input('step'),
                'data' => $data,
                'updated_at' => now(),
            ], now()->addDays(7));

            // Maintain list of draft IDs for this user
            $draftIds = Cache::get("{$userID}_transportation_draft_ids", []);
            if (!in_array($draftId, $draftIds)) {
                $draftIds[] = $draftId;
                Cache::put("{$userID}_transportation_draft_ids", $draftIds, now()->addDays(7));
            }

            return response()->json(['success' => true, 'draftId' => $draftId]);
        } catch (ValidationException $e) {
            // Validation failed - return errors in the format expected by frontend
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
