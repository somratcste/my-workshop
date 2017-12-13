function displayMenu($nodes, $indent=0) {
    foreach ($nodes as $node) {
        print str_repeat('&nbsp;',$indent*4);
        print $node['name'];
        print '<br/>';
        if (isset($node['child']))
            displayMenu($node['child'],$indent+1);
    }
}

Route::get('test', function () {
    $array = trans('menu.primary');
    displayMenu($array);
});


========= With style and active menu open ================= 

@php
    function displayMenu($nodes, &$menu) {
        foreach ($nodes as $node) {

                $f = 0;
                if((isset($node['child']) && count($node['child']))) {
                    $f = 1;
                }

                $url = '';
                if(isset($node['route'])) {
                    $url = $node['route'];
                }elseif(isset($node['url'])) {
                    $url = $node['url'];
                }

                $requestUrl = \Request::url();
                $flag = 0;
                if($url === $requestUrl){
                    $flag = 1;
                    $menu .= '<li '.( $f ? 'class="treeview active"' : '').'>';
                }
                else {
                    $menu .= '<li '.( $f ? 'class="treeview"' : '').'>';
                }

               $menu .= '<a href="'.$url.'">';
               $menu .= isset($node['icon']) ? '<i class="'.$node['icon'].'"></i>' : '';
               $menu .= '<span>'.$node['name'].'</span>';
               $menu .= ( $f ? '<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>' : '');
               $menu .= '</a>';

            if ($f) {
                if($flag) {
                    $menu.= '<ul class="treeview-menu" style="display: none;">';
                } else {
                    $menu.= '<ul class="treeview-menu">';
                }
                displayMenu($node['child'],$menu);
                $menu .= '</ul>';
            }
            $menu .= '</li>';
        }
    }

@endphp
@php
$menu = '';
@endphp
{{ displayMenu(trans('menu.primary'), $menu) }}
{!! $menu !!}

======== End  ======= 

======== For array structure =========  

return [
    'primary' => [
        [
            'name'  => 'Area',
            'icon'  => 'fa fa-globe',
            'url'   => '#',
            'child' => [
                [
                    'name'  => 'Create',
                    'icon'  => 'fa fa-circle-o',
                    'route'   => route('crud.area.create')
                ],
                [
                    'name'  => 'Area Lists',
                    'icon'  => 'fa fa-circle-o',
                    'route'   => route('crud.area.index')
                ]
            ]
        ],
        [
            'name'  => 'Attribute',
            'icon'  => 'fa fa-tags',
            'url'   => '#',
            'child' => [
                [
                    'name'  => 'Create',
                    'icon'  => 'fa fa-circle-o',
                    'url'   => route('crud.attribute.create')
                ],
                [
                    'name'  => 'Attribute Lists',
                    'icon'  => 'fa fa-circle-o',
                    'route'   => route('crud.attribute.index')
                ]
            ]
        ]
    ]
]

======= structure for menu ================ 
