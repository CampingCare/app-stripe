<?php

namespace App;

class StripeApp
{
    public static function getTryAgainUrl($stripePayment)
    {
        $stripePaymentData = json_decode($stripePayment->data);

        if (isset($stripePaymentData->line_items)) {
            $amount = substr($stripePaymentData->line_items[0]->price_data->unit_amount, 0, -2) . '.' .
                        substr($stripePaymentData->line_items[0]->price_data->unit_amount, -2);

            $currency = strtoupper($stripePaymentData->line_items[0]->price_data->currency);
        } else {
            $amount = $stripePaymentData->amount;
            $currency = $stripePaymentData->currency;
        }

        $params = [];
        $params['admin_id'] = $stripePayment->admin_id;
        $params['amount'] = $amount;
        $params['currency'] = $currency;

        if (isset($stripePaymentData->metadata->reservation_id))
            $params['reservation_id'] = $stripePaymentData->metadata->reservation_id;

        if (isset($stripePaymentData->metadata->reservation_number))
            $params['reservation_number'] = $stripePaymentData->metadata->reservation_number;

        if (isset($stripePaymentData->metadata->invoice_id))
            $params['invoice_id'] = $stripePaymentData->metadata->invoice_id;

        return '/api/webhooks/payment-request?' . http_build_query($params);
    }

    public static function getMethodId($stripeMethodKey)
    {
        switch (strtolower($stripeMethodKey)) {
            case 'bancontact':
                return 15;
            case 'card':
                return 21;
            case 'card_present':
                return 4;
            case 'eps':
                return 11;
            case 'giropay':
                return 22;
            case 'ideal':
                return 8;
            case 'klarna':
                return 13;
            case 'sofort':
                return 13;

            default:
                return 1;
        }
    }
}
