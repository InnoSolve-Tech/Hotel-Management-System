@extends('layouts.app')

@section('content')
    @php
        $currentUser = auth()->user();

        $overviewMetrics = [
            [
                'label' => 'Total Floors',
                'value' => $TotalFloor,
                'icon' => 'fa-brands fa-buffer',
                'tone' => 'sage',
                'note' => 'Floor entries currently configured.',
            ],
            [
                'label' => 'Free Rooms',
                'value' => $TotalFreeRooms,
                'icon' => 'fa-brands fa-buromobelexperte',
                'tone' => 'sky',
                'note' => 'Rooms ready for the next booking.',
            ],
            [
                'label' => 'Booked Rooms',
                'value' => $TotalBookedRooms,
                'icon' => 'fa-solid fa-house-circle-check',
                'tone' => 'amber',
                'note' => 'Rooms already occupied or reserved.',
            ],
            [
                'label' => 'Total Deposits',
                'value' => $TotalDeposit,
                'icon' => 'fa-solid fa-money-bill-trend-up',
                'tone' => 'mint',
                'note' => 'Deposit records captured so far.',
            ],
            [
                'label' => 'Total Withdrawals',
                'value' => $TotalWithdraw,
                'icon' => 'fa-solid fa-money-bill-transfer',
                'tone' => 'slate',
                'note' => 'Withdrawal records captured so far.',
            ],
        ];

        $resourceMetrics = [
            [
                'label' => 'Users',
                'value' => $TotalUser,
                'icon' => 'fas fa-user-plus',
                'tone' => 'sage',
                'caption' => 'People with access to Hot-L.',
                'link' => $currentUser?->hasPermission('user.view') ? url('/user') : null,
            ],
            [
                'label' => 'Rooms',
                'value' => $TotalRooms,
                'icon' => 'fa-brands fa-buromobelexperte',
                'tone' => 'sky',
                'caption' => 'Rooms currently registered.',
                'link' => $currentUser?->hasPermission('room.view') ? url('/room') : null,
            ],
            [
                'label' => 'Team Members',
                'value' => $TotalEmployee,
                'icon' => 'fa-solid fa-people-roof',
                'tone' => 'amber',
                'caption' => 'Team members on the platform.',
                'link' => $currentUser?->hasPermission('employee.view') ? url('/employee') : null,
            ],
            [
                'label' => 'Guests',
                'value' => $TotalGuest,
                'icon' => 'fa-solid fa-person-walking-luggage',
                'tone' => 'mint',
                'caption' => 'Guest profiles available for booking.',
                'link' => $currentUser?->hasPermission('guest.view') ? url('/guest') : null,
            ],
            [
                'label' => 'Banks',
                'value' => $TotalBank,
                'icon' => 'fa-solid fa-building-columns',
                'tone' => 'gold',
                'caption' => 'Connected banks and cash sources.',
                'link' => $currentUser?->hasPermission('bank.view') ? url('/bank') : null,
            ],
            [
                'label' => 'Account Numbers',
                'value' => $TotalAccountNo,
                'icon' => 'fa-solid fa-money-check-dollar',
                'tone' => 'slate',
                'caption' => 'Bank accounts stored in the system.',
                'link' => $currentUser?->hasPermission('acount/ledger.view') ? url('/acount/ledger') : null,
            ],
        ];
    @endphp

    <div class="container-fluid py-4 hotelio-dashboard">
        <div class="hotelio-dashboard__header">
            <div>
                <span class="hotelio-dashboard__eyebrow">Operations Snapshot</span>
                <h1 class="hotelio-dashboard__title">Dashboard</h1>
                <p class="hotelio-dashboard__subtitle">
                    A calmer view of rooms, bookings, finance, and people across Hot-L.
                </p>
            </div>
            <div class="hotelio-dashboard__legend">
                <span class="hotelio-room-pill hotelio-room-pill--free">Free {{ $TotalFreeRooms }}</span>
                <span class="hotelio-room-pill hotelio-room-pill--booked">Booked {{ $TotalBookedRooms }}</span>
                <span class="hotelio-room-pill hotelio-room-pill--neutral">Total {{ $TotalRooms }}</span>
            </div>
        </div>

        <div class="row">
            @foreach ($overviewMetrics as $metric)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="hotelio-metric-card hotelio-metric-card--{{ $metric['tone'] }}">
                        <div class="hotelio-metric-card__icon">
                            <i class="{{ $metric['icon'] }}"></i>
                        </div>
                        <div class="hotelio-metric-card__content">
                            <p class="hotelio-metric-card__label">{{ $metric['label'] }}</p>
                            <h3 class="hotelio-metric-card__value counter">{{ $metric['value'] }}</h3>
                            <p class="hotelio-metric-card__note">{{ $metric['note'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            @foreach ($resourceMetrics as $metric)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="hotelio-stat-card hotelio-stat-card--{{ $metric['tone'] }}">
                        <div class="hotelio-stat-card__body">
                            <div class="hotelio-stat-card__content">
                                <p class="hotelio-stat-card__label">{{ $metric['label'] }}</p>
                                <h3 class="hotelio-stat-card__value counter">{{ $metric['value'] }}</h3>
                                <p class="hotelio-stat-card__caption">{{ $metric['caption'] }}</p>
                            </div>
                            <div class="hotelio-stat-card__icon">
                                <i class="{{ $metric['icon'] }}"></i>
                            </div>
                        </div>
                        @if ($metric['link'])
                            <a href="{{ $metric['link'] }}" class="hotelio-stat-card__footer">
                                Open {{ $metric['label'] }} <i class="fas fa-arrow-right"></i>
                            </a>
                        @else
                            <span class="hotelio-stat-card__footer hotelio-stat-card__footer--muted">
                                Visible based on your permissions
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card hotelio-dashboard-card">
            <div class="card-header hotelio-dashboard-card__header">
                <div>
                    <h5 class="mb-1">Room availability</h5>
                    <p class="mb-0 text-muted">
                        Total rooms: <span class="counter">{{ $TotalRooms }}</span>
                        · Booked: <span class="counter">{{ $TotalBookedRooms }}</span>
                        · Free: <span class="counter">{{ $TotalFreeRooms }}</span>
                    </p>
                </div>
                <div class="hotelio-dashboard__legend">
                    <span class="hotelio-room-pill hotelio-room-pill--free">Available</span>
                    <span class="hotelio-room-pill hotelio-room-pill--booked">Booked</span>
                </div>
            </div>
            <div class="card-body">
                @if ($Rooms->isEmpty())
                    <div class="hotelio-empty-state">
                        <i class="fa-solid fa-door-open"></i>
                        <p class="mb-0">No rooms have been added yet.</p>
                    </div>
                @else
                    <div class="hotelio-room-grid">
                        @foreach ($Rooms as $Room)
                            <div class="hotelio-room-card {{ $Room->Status ? 'is-booked' : 'is-free' }}">
                                <span class="hotelio-room-card__status"></span>
                                <div class="hotelio-room-card__content">
                                    <h6 class="mb-1">{{ $Room->RoomNo }}</h6>
                                    <p class="mb-0">
                                        {{ $Room->Status ? 'Booked' : 'Available' }}
                                        @if (filled($Room->Floor))
                                            · Floor {{ $Room->Floor }}
                                        @endif
                                    </p>
                                </div>
                                <i class="hotelio-room-card__icon {{ $Room->Status ? 'fa-solid fa-house-circle-check' : 'fa-brands fa-buromobelexperte' }}"></i>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
