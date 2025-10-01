class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_vehicles' => Vehicle::count(),
            'vehicles_in_maintenance' => Vehicle::where('status', 'maintenance')->count(),
            'upcoming_maintenance' => Vehicle::where('next_maintenance_date', '<=', now()->addDays(7))->get(),
            'active_drivers' => Driver::where('status', 'active')->count()
        ];

        return view('dashboard.index', compact('data'));
    }
}
