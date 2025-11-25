<?php

namespace App\Http\Traits;

use Illuminate\Http\RedirectResponse;

trait HandlesServiceExceptions
{
    /**
     * Generate a friendly error message based on environment
     *
     * @param string $context The context of the error (e.g., "crear el permiso", "actualizar el rol")
     * @param \Throwable $e The exception that was thrown
     * @return string
     */
    protected function friendlyError(string $context, \Throwable $e): string
    {
        return app()->isLocal()
            ? "Error al {$context}: {$e->getMessage()}"
            : "Ocurrió un error al {$context}. Por favor, intenta nuevamente.";
    }

    /**
     * Handle service exceptions with consistent error responses
     *
     * @param \Throwable $e The exception that was thrown
     * @param string|null $customMessage Optional custom error message (defaults to exception message)
     * @param bool $logError Whether to log the error (default: true)
     * @return RedirectResponse
     */
    protected function handleServiceError(
        \Throwable $e,
        ?string $customMessage = null,
        bool $logError = true
    ): RedirectResponse {
        if ($logError) {
            logger()->error('Service exception occurred', [
                'auth_user_id' => auth()->id(),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return redirect()->back()
            ->with('error', $customMessage ?? $e->getMessage());
    }

    /**
     * Execute a service action with automatic error handling
     *
     * @param callable $action The service action to execute
     * @param string $successMessage Message to show on success
     * @param string|null $errorMessage Optional custom error message
     * @param bool $logError Whether to log errors
     * @param bool $refresh Signal frontend to refresh data (default: true)
     * @return RedirectResponse
     */
    protected function executeServiceAction(
        callable $action,
        string $successMessage,
        ?string $errorMessage = null,
        bool $logError = true,
        bool $refresh = true
    ): RedirectResponse {
        try {
            $action();

            $flashData = ['success' => $successMessage];

            if ($refresh) {
                $flashData['refresh'] = true;
            }

            return redirect()->back()->with($flashData);
        } catch (\Throwable $e) {
            return $this->handleServiceError($e, $errorMessage, $logError);
        }
    }

    /**
     * Return a success redirect response
     *
     * @param string $message Success message to display
     * @param bool $withInput Whether to keep old input (default: false)
     * @return RedirectResponse
     */
    protected function successResponse(string $message, bool $withInput = false): RedirectResponse
    {
        $response = redirect()->back()->with('success', $message);

        return $withInput ? $response->withInput() : $response;
    }

    /**
     * Return an error redirect response
     *
     * @param string $message Error message to display
     * @param bool $withInput Whether to keep old input (default: true for re-submission)
     * @return RedirectResponse
     */
    protected function errorResponse(string $message, bool $withInput = true): RedirectResponse
    {
        $response = redirect()->back()->with('error', $message);

        return $withInput ? $response->withInput() : $response;
    }

    /**
     * Return a warning redirect response
     *
     * @param string $message Warning message to display
     * @param bool $withInput Whether to keep old input (default: false)
     * @return RedirectResponse
     */
    protected function warningResponse(string $message, bool $withInput = false): RedirectResponse
    {
        $response = redirect()->back()->with('warning', $message);

        return $withInput ? $response->withInput() : $response;
    }

    /**
     * Return an info redirect response
     *
     * @param string $message Info message to display
     * @param bool $withInput Whether to keep old input (default: false)
     * @return RedirectResponse
     */
    protected function infoResponse(string $message, bool $withInput = false): RedirectResponse
    {
        $response = redirect()->back()->with('info', $message);

        return $withInput ? $response->withInput() : $response;
    }

    /**
     * Return a validation error redirect response
     *
     * Specifically for validation failures after manual checks
     *
     * @param string $message Validation error message
     * @return RedirectResponse
     */
    protected function validationErrorResponse(string $message): RedirectResponse
    {
        return redirect()->back()
            ->with('error', $message)
            ->withInput();
    }
}
