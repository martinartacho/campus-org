<?php

namespace App\View\Components\Dashboard;

use App\Http\Controllers\CalendarController;
use Illuminate\View\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Models\UserSetting;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;

class Basic extends Component
{
    public $currentLanguage;
    public $globalLanguage;
    public $userLanguage;
     public $events;
    
    public function __construct()
    {
        $this->currentLanguage = App::getLocale();
        $this->globalLanguage = Cache::remember('global_language', now()->addDay(), function () {
            return Setting::where('key', 'language')->value('value') ?? config('app.locale');
        });
        $this->userLanguage = Auth::check() 
            ? UserSetting::where('user_id', Auth::id())
                         ->where('key', 'language')
                         ->value('value')
            : null;

        
         // Próximos eventos (solo si el usuario tiene permiso para ver el calendario)
        $this->events = Auth::check() && Auth::user()->can('view-calendar')
            ? Event::whereDate('start', '>=', now())
                ->orderBy('start', 'asc')
                ->take(5)
                ->get()
            : collect([]);

        
    }


    
    public function render()
    {
        return view('components.dashboard.basic');
    }
}