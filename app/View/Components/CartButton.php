<?php

namespace App\View\Components;

use App\Helpers\JWTAuthHelper;
use App\Interfaces\KeranjangServiceInterface;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CartButton extends Component
{
    private $keranjangService;
    private $jwtAuthHelper;
    /**
     * Create a new component instance.
     */
    public function __construct(
        KeranjangServiceInterface $keranjangService,
        JWTAuthHelper $jwtAuthHelper
    ) {
        $this->keranjangService = $keranjangService;
        $this->jwtAuthHelper = $jwtAuthHelper;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $user = $this->jwtAuthHelper->getUser();
        $count = $this->keranjangService->getTotalCount($user);
        return view('components.cart-button', [
            'count' => $count
        ]);
    }
}