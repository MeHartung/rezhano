    #app/config/config.yml
    accurateweb_cdek_shipping:
        tariffs:
            courier: !php/const: AccurateCommerce\Component\CdekShipping\Api\CdekApiClient::TARIFF_PARCEL_STORAGE_DOOR
            pickup: !php/const: AccurateCommerce\Component\CdekShipping\Api\CdekApiClient::TARIFF_PARCEL_STORAGE_STORAGE