<?php

namespace Morningtrain\NETSEasy\Wordpress\Http;

use Morningtrain\NETSEasy\Helpers\NetsEasyPaymentStatus;
use Morningtrain\NETSEasy\Model\PaymentReference;

class PaymentHandleWebhook implements \Morningtrain\NETSEasy\Contracts\PaymentHandleWebhook
{
    public function __construct()
    {
    }

    public function url(string $event = '{event}'): string
    {
        return \rest_url("morningtrain/nets-easy/v1/webhook/{$event}");
    }

    public function register(): void
    {
        \add_action('rest_api_init', $this->registerRestRoute(...));
    }

    public function registerRestRoute(): void
    {
        \register_rest_route(
            'morningtrain/nets-easy/v1',
            '/webhook/(?P<event>[a-zA-Z0-9.]+)',
            [
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => $this->handle(...),
                'permission_callback' => $this->checkPermission(...),
            ]
        );
    }

    public function handle(\WP_REST_Request $request): \WP_REST_Response|\WP_Error
    {
        $webhookName = $request->get_param('event');
        $webhookNames = config('nets-easy.events', []);

        if (! array_key_exists($webhookName, $webhookNames)) {
            return new \WP_Error('no_webhook', __('Invalid webhook'), ['status' => 404]);
        }

        $data = $request->get_param('data');
        $paymentId = $data['paymentId'] ?? null;

        if (empty($paymentId)) {
            return new \WP_Error('invalid_payment_id', __('Invalid payment ID'), ['status' => 404]);
        }

        $paymentReference = PaymentReference::query()
            ->where('payment_id', $paymentId)
            ->first();

        if (empty($paymentReference)) {
            return new \WP_Error('invalid_payment_id', __('Invalid payment ID'), ['status' => 404]);
        }

        $paymentReferenceWebhookIds = is_array($paymentReference->webhook_ids) ? $paymentReference->webhook_ids : [];
        $webhookId = $request->get_param('id');

        if (in_array($webhookId, $paymentReferenceWebhookIds)) {
            return new \WP_Error('webhook_handled', __('Webhook already handled'), ['status' => 200]);
        }

        app(NetsEasyPaymentStatus::class)->handleStatus($webhookName, $paymentReference);

        $paymentReferenceWebhookIds[] = $webhookId;
        $paymentReference->webhook_ids = $paymentReferenceWebhookIds;

        // TODO: Dispatch the event, when \Illuminate\Foundation\Events\Dispatchable is available in Medley
        // PaymentEventDispatcher::dispatch($webhookName, $paymentReference, $data);

        try {
            \do_action("morningtrain/nets-easy/webhook/{$webhookName}", $paymentReference, $data);
            \do_action('morningtrain/nets-easy/webhook', $paymentReference, $data, $webhookName);
        } catch (\Exception $exception) {
            return new \WP_Error($exception->getCode(), $exception->getMessage());
        }

        $paymentReference->save();

        return new \WP_REST_Response;
    }

    public function checkPermission(\WP_REST_Request $request): bool
    {
        return config('nets-easy.auth-key') === $request->get_header('Authorization');
    }
}
