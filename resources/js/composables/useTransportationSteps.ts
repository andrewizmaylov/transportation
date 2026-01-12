export function useTransportationSteps(transportationId: string) {
    const steps = [
        'transportationStep',
        'pickupAddressStep',
        'deliveryAddressStep',
        'cargoStep',
        'confirmStep'
    ];

    function getRoute(currentStep: string): string {
        switch (currentStep) {
            case 'transportationStep':
                return `/public/api/v1/shipper/register-transportation`;
            case 'pickupAddressStep':
                return `/public/api/v1/shipper/${transportationId}/add-transportation-address`;
            case 'deliveryAddressStep':
                return `/public/api/v1/shipper/${transportationId}/add-transportation-address`;
            case 'cargoStep':
                return `/public/api/v1/shipper/${transportationId}/add-cargo`;
            case 'confirmationStep':
                return `/public/api/v1/shipper/${transportationId}/confirm`;
            default:
                return `/public/api/v1/shipper/register-transportation`;
        }
    }

    return {
        steps,
        getRoute,
    }
}
