<?php

namespace Innopanda\AssetManager\View\Components;

use Illuminate\View\Component;


class ChooseImage extends Component
{

    public function render()
    {
        return view(
            'asset-manager::components.choose-image'
        );
    }

}