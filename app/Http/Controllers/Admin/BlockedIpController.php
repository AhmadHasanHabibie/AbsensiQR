<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedIp;
use App\Models\LoginHistory;
use Illuminate\Http\Request;

class BlockedIpController extends Controller
{
    /**
     * Menampilkan daftar IP Address yang diblokir untuk Admin.
     */
    public function index(Request $request)
    {
        $query = BlockedIp::orderBy('updated_at', 'desc');

        // Summary Metric Cards
        $totalBlocked   = BlockedIp::count();
        $permanentCount = BlockedIp::where('is_permanent', true)->count();
        $temporaryCount = BlockedIp::where('is_permanent', false)
            ->where('blocked_until', '>', now())
            ->count();
        $expiredCount   = BlockedIp::where('is_permanent', false)
            ->where('blocked_until', '<=', now())
            ->count();

        if ($request->filled('status')) {
            match ($request->status) {
                'permanent' => $query->where('is_permanent', true),
                'temporary' => $query->where('is_permanent', false)->where('blocked_until', '>', now()),
                'expired'   => $query->where('is_permanent', false)->where('blocked_until', '<=', now()),
                default     => null,
            };
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        $blockedIps = $query->paginate(15)->withQueryString();

        // Attach Metric Breakdown ke setiap record
        $blockedIps->getCollection()->transform(function ($item) {
            $ip = $item->ip_address;

            $failedLogins = LoginHistory::where('ip_address', $ip)
                ->where('login_status', 'failed')
                ->count();

            $item->failed_logins = $failedLogins;

            return $item;
        });

        return view('Admin.BlockedIp.Index', compact(
            'totalBlocked',
            'temporaryCount',
            'permanentCount',
            'expiredCount',
            'blockedIps'
        ));
    }

    /**
     * Membuka pemblokiran IP Address (Unblock / Expire).
     */
    public function unblock(BlockedIp $blockedIp)
    {
        $blockedIp->update([
            'is_permanent'  => false,
            'blocked_until' => now(),
        ]);

        return back()->with('success', "Pemblokiran IP {$blockedIp->ip_address} berhasil dibuka.");
    }

    /**
     * Mengubah status pemblokiran IP menjadi Permanent Block (Blacklist).
     */
    public function makePermanent(BlockedIp $blockedIp)
    {
        $blockedIp->update([
            'is_permanent'  => true,
            'blocked_until' => null,
        ]);

        return back()->with('success', "IP {$blockedIp->ip_address} berhasil diubah menjadi Permanent Block (Blacklist).");
    }
}
