<?php


// FILE: app/Http/Controllers/API/V1/Financial/WalletController.php
// ============================================================
namespace App\Http\Controllers\API\V1\Financial;

use App\DTOs\Financial\{SplitBillDTO, TopUpDTO};
use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\{SplitBillRequest, TopUpRequest, WithdrawRequest};
use App\Http\Resources\Financial\TransactionResource;
use App\Services\{SplitBillService, WalletService};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Wallet", description="Ledger-based wallet system")
 */
class WalletController extends Controller
{
    public function __construct(
        private readonly WalletService    $walletService,
        private readonly SplitBillService $splitBillService,
    ) {}

    /**
     * @OA\Get(path="/api/v1/wallet", tags={"Wallet"}, security={{"sanctum":{}}})
     */
    public function balance(Request $request): JsonResponse
    {
        return response()->json([
            'balance'      => $this->walletService->getBalance($request->user()->id),
            'transactions' => TransactionResource::collection(
                $this->walletService->getTransactionHistory($request->user()->id)
            ),
        ]);
    }

    /**
     * @OA\Post(path="/api/v1/wallet/top-up", tags={"Wallet"}, security={{"sanctum":{}}})
     */
    public function topUp(TopUpRequest $request): JsonResponse
    {
        $this->walletService->topUp(
            $request->user()->id,
            (float) $request->amount,
            $request->reference,
        );

        return response()->json(['message' => 'Wallet topped up successfully.']);
    }

    /**
     * @OA\Post(path="/api/v1/wallet/withdraw", tags={"Wallet"}, security={{"sanctum":{}}})
     */
    public function withdraw(WithdrawRequest $request): JsonResponse
    {
        $this->walletService->withdraw(
            $request->user()->id,
            (float) $request->amount,
        );

        return response()->json(['message' => 'Withdrawal request submitted.']);
    }

    /**
     * @OA\Post(path="/api/v1/wallet/split-bill", tags={"Wallet"}, security={{"sanctum":{}}})
     */
    public function splitBill(SplitBillRequest $request): JsonResponse
    {
        $this->splitBillService->initiateSplit(
            SplitBillDTO::fromArray($request->validated(), $request->user()->id)
        );

        return response()->json(['message' => 'Split bill requests sent to teammates.']);
    }
}