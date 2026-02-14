@extends('me::master')

@section('title', trans('Dashboard'))

@section('content')
<div class="container-fluidx py-2">
	{{-- Top stat cards --}}
	<div class="row mb-4">
        <div class="col-lg-6 col-md-12 col-sm-12 mb-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4 dashboard-card bg-gradient-dark animate__animated animate__fadeInRightBig animate__zoomIn text-center text-light">
                        <div class="card-body">
                            <div class="d-flex justify-content-center mb-0">
                                <canvas id="analog-clock" width="260" height="260"></canvas>
                            </div>
                            <p class="small mb-0 mt-1 opacity-75">Timezone: <strong>{{ date_default_timezone_get() }}</strong></p>
                        </div>
                    </div>
                    <div class="card mb-4 dashboard-card bg-gradient-warning animate__animated animate__fadeInRightBig animate__zoomIn wow-card" data-wow-delay="0.4s">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-images fa-2x me-3 icon-glow"></i>
                                <div>
                                    <h5 class="card-title mb-0">Gallery Images</h5>
                                    <p class="card-text fs-4 counter" data-target="{{ \ME\EmCore\Models\Gallery::count() }}">{{ \ME\EmCore\Models\Gallery::count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4 dashboard-card bg-gradient-danger animate__animated animate__fadeInRightBig animate__zoomIn wow-card text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar fa-2x me-3 icon-pulse-fast"></i>
                                <div>
                                    <h5 class="card-title mb-0">Events</h5>
                                    <p class="card-text fs-4 counter" data-target="{{ \ME\EmCore\Models\Event::count() }}">{{ \ME\EmCore\Models\Event::count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4 dashboard-card bg-gradient-primary animate__animated animate__fadeInRightBig animate__zoomIn wow-card" data-wow-delay="0.1s">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-users fa-2x me-3 icon-spin"></i>
                                <div>
                                    <h5 class="card-title mb-0">Users</h5>
                                    <p class="card-text fs-4 counter" data-target="{{ \ME\Models\User::count() }}">{{ \ME\Models\User::count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4 dashboard-card bg-gradient-success animate__animated animate__fadeInRightBig animate__zoomIn animate__jackInTheBox wow-card" data-wow-delay="0.2s">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-envelope fa-2x me-3 icon-bounce"></i>
                                <div>
                                    <h5 class="card-title mb-0">Messages</h5>
                                    <p class="card-text fs-4 counter" data-target="{{ \ME\EmCore\Models\Message::count() }}">{{ \ME\EmCore\Models\Message::count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4 dashboard-card bg-gradient-info animate__animated animate__fadeInRightBig animate__zoomIn animate__fadeInDownBig wow-card" data-wow-delay="0.3s">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-alt fa-2x me-3 icon-pulse"></i>
                                <div>
                                    <h5 class="card-title mb-0">Documents</h5>
                                    <p class="card-text fs-4 counter" data-target="{{ \ME\EmCore\Models\Document::count() }}">{{ \ME\EmCore\Models\Document::count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4 dashboard-card bg-gradient-secondary animate__animated animate__fadeInRightBig animate__zoomIn animate__fadeInUp wow-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-shield fa-2x me-3 icon-spin-slow"></i>
                                <div>
                                    <h5 class="card-title mb-0">Roles</h5>
                                    <p class="card-text fs-4 counter" data-target="{{ \ME\Models\Role::count() }}">{{ \ME\Models\Role::count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card dashboard-card bg-gradient-light animate__animated animate__fadeInRightBig animate__zoomIn animate__lightSpeedInLeft wow-card text-dark settings-card" data-wow-delay="0.4s">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-cogs fa-2x me-3 icon-gear"></i>
                                <div>
                                    <h5 class="card-title mb-0">Settings</h5>
                                    <p class="card-text fs-4 counter" data-target="{{ \ME\Models\Setting::count() }}">{{ \ME\Models\Setting::count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card mb-4 dashboard-card event-card animate__animated animate__fadeInRightBig animate__zoomIn animate__fadeInDownBig wow-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="row w-100">
                                    <div class="col-md-3 text-center border-end">
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <!-- Icon -->
                                            <i class="fas fa-calendar-day fa-2x me-3 icon-pulse"></i>

                                            <!-- Title + Count -->
                                            <div class="text-start">
                                                <h5 class="card-title mb-0">Today's Events</h5>
                                                <p class="card-text fs-4 counter mb-0" data-target="{{ \ME\EmCore\Models\Event::whereDate('start', today())->count() }}">
                                                    {{ \ME\EmCore\Models\Event::whereDate('start', today())->count() }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-9 ps-4" style="margin-top: auto; margin-bottom: auto;">
                                        @if(\ME\EmCore\Models\Event::whereDate('start', today())->exists())
                                            <h5 class="card-title mb-2">{{ \ME\EmCore\Models\Event::whereDate('start', today())->pluck('title')->join(', ') ?: 'N/A' }}</h5>
                                        @else
                                            <h5 class="card-title mb-0 w-100 text-muted" align="center">No Events Today</h5>
                                        @endif
                                     </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12 mb-4">
            <div class="card dashboard-card bg-encodex-light animate__animated  animate__fadeInRightBig animate__fadeInLeftBig">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 d-none">
                        <h5 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i> Calendar</h5>
                        <button id="add-event-btn" class="btn btn-sm btn-encodex">+ Add Event</button>
                    </div>
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
	</div>
</div>
@endsection

@push('css')
	<!-- Animate.css -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
	<!-- FullCalendar CSS (uses global build) -->
	<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

	<style>
		/* Card base */
		.dashboard-card {
			box-shadow: 0 12px 36px rgba(0,0,0,0.22);
			border: none;
			border-radius: 1.25rem;
			transition: transform 0.45s cubic-bezier(.2,.9,.2,1), box-shadow 0.45s;
			overflow: visible;
			position: relative;
		}
		.dashboard-card:hover {
			transform: translateY(-14px) scale(1.03) rotate(-0.6deg);
			box-shadow: 0 30px 80px rgba(0,0,0,0.35);
		}
		.dashboard-card::before {
			content: '';
			position: absolute;
			inset: -2px;
			pointer-events: none;
			border-radius: 1.3rem;
			background: linear-gradient(45deg, rgba(255,255,255,0.03), rgba(0,0,0,0.02));
			opacity: 0.9;
			mix-blend-mode: overlay;
		}

        .event-card {
            background: linear-gradient(135deg, #2b6e8a 0%, #b24425 100%);
            color: white;
		}

		/* Gradients (override safe colors) */
		.bg-gradient-primary { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: #fff; }
		.bg-gradient-success { background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%); color: #fff; }
		.bg-gradient-info    { background: linear-gradient(135deg, #36b9cc 0%, #258fa7 100%); color: #fff; }
		.bg-gradient-warning { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); color: #fff; }
		.bg-gradient-secondary { background: linear-gradient(135deg, #858796 0%, #6c757d 100%); color: #fff; }
		.bg-gradient-dark    { background: linear-gradient(135deg, #142c54 0%, #121416 100%); color: #0016ff94 !important; }
		.bg-gradient-light   { background: linear-gradient(135deg, #f8f9fc 0%, #e3e6f0 100%); color: #222; }
		.bg-gradient-danger  { background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%); color: #fff; }

		/* Icon micro animations */
		.icon-spin { animation: spin 8s linear infinite; display:inline-block;}
		.icon-spin-slow { animation: spin 18s linear infinite; display:inline-block; }
		.icon-bounce { animation: bounce 1.6s infinite; display:inline-block;}
		.icon-bounce-slow { animation: bounce 3s infinite; display:inline-block; }
		.icon-pulse { animation: pulse 1.8s infinite; display:inline-block;}
		.icon-pulse-fast { animation: pulse 1s infinite; display:inline-block;}
		.icon-glow { animation: glow 2.5s ease-in-out infinite alternate; display:inline-block;}
		.icon-gear { transform-origin:center; animation: gear 6s linear infinite; display:inline-block;}

		@keyframes spin { 0% { transform: rotate(0);} 100% { transform: rotate(360deg);} }
		@keyframes bounce { 0%,100% { transform: translateY(0);} 50% { transform: translateY(-8px);} }
		@keyframes pulse { 0% { transform: scale(1);} 50% { transform: scale(1.12);} 100% { transform: scale(1);} }
		@keyframes glow {
			from { text-shadow: 0 0 6px rgba(255,255,255,0.8), 0 0 18px rgba(0, 255, 200, 0.75);}
			to   { text-shadow: 0 0 18px rgba(255,180,0,0.95), 0 0 36px rgba(255,0,120,0.65);}
		}
		@keyframes gear { 0% { transform: rotate(0);} 100% { transform: rotate(-360deg);} }


		/* Counter style */
		.counter { font-weight: 700; letter-spacing: 0.3px; }

		/* Calendar styling */
        #calendar {
			background: #fff;
			border-radius: 10px;
			padding: 0.6rem;
			min-height: 320px;
			box-shadow: 0 6px 20px rgba(0,0,0,0.15);
		}

        /* === FullCalendar custom styling === */

        /* Calendar header (month/week/day title + prev/next buttons) */
        .fc .fc-toolbar.fc-header-toolbar {
            background: linear-gradient(135deg, #224abe, #4e73df); /* 🔵 Blue gradient */
            color: #fff;
            padding: 6px 10px;
            border-radius: 8px 8px 0 0;
        }

        /* Header buttons (prev/next/today) */
        .fc .fc-button {
            background: #fff;
            border: none;
            color: #224abe;
            font-weight: 600;
            border-radius: 6px;
            padding: 4px 10px;
            margin: 0 2px;
            transition: 0.2s;
        }
        .fc .fc-button:hover {
            background: #224abe;
            color: #fff;
        }

        /* Month/Week/Day title text */
        .fc .fc-toolbar-title {
            font-size: 1rem;
            font-weight: 600;
            color: #fff !important;
        }



        .fc-day{
            height: 2rem !important;
            width: 5rem !important;
        }
        .fc-daygrid-day{
                height: 2rem !important;
            width: 5rem !important;
        }



		@keyframes calendarGlow {
			from { box-shadow: 0 12px 30px rgba(255,150,255,0.12); transform: translateY(0); }
			to   { box-shadow: 0 30px 70px rgba(255,0,150,0.18); transform: translateY(-6px); }
		}

		/* Neon digital clock */
		.neon-clock {
			color: #00f5ff;
			text-shadow: 0 0 6px #00f5ff, 0 0 18px #00a6ff, 0 0 28px #00f5ff;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
			animation: neonPulse 2s infinite;
		}
		@keyframes neonPulse {
			0% { transform: scale(1); opacity: 1; }
			50% { transform: scale(1.06); opacity: 0.82; }
			100% { transform: scale(1); opacity: 1; }
		}

		/* Analog canvas container adjustments */
		canvas#analog-clock { border-radius: 50%; box-shadow: 0 12px 40px rgba(0,0,0,0.5), 0 0 30px rgba(0,255,200,0.06); background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.02), rgba(0,0,0,0.35)); }

		/* Small screens */
		@media (max-width: 767.98px) {
			.dashboard-card { transform: none !important; }
			#calendar { min-height: 180px; } /* reduced from 300px */
            .settings-card{
                margin-bottom:1.5rem !important;
            }
		}
	</style>
@endpush

@push('js')
	<!-- FullCalendar (global bundle) -->
	<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

	<script>
		/* -------------------------
		   Counter up (smooth)
		   ------------------------- */
		(function(){
			const counters = document.querySelectorAll('.counter');
			counters.forEach(el => {
				const target = parseInt(el.dataset.target ?? el.innerText) || 0;
				let current = 0;
				const duration = 1200; //ms
				const stepTime = Math.max(15, Math.floor(duration / Math.max(target,1)));
				const increment = Math.max(1, Math.floor(target / (duration / stepTime)));
				const updater = () => {
					current += increment;
					if(current >= target) {
						el.textContent = target.toLocaleString();
					} else {
						el.textContent = current.toLocaleString();
						setTimeout(updater, stepTime);
					}
				};
				// small delay for staggered effect
				setTimeout(updater, 200 + Math.random()*600);
			});
		})();

		/* -------------------------
		   FullCalendar init
		   ------------------------- */
		document.addEventListener('DOMContentLoaded', function() {
			const calendarEl = document.getElementById('calendar');
			const isMobile = window.matchMedia('(max-width: 767.98px)').matches;
			const calendar = new FullCalendar.Calendar(calendarEl, {
				initialView: 'dayGridMonth',
				headerToolbar: isMobile
					? { left: 'prev', center: 'title', right: 'next' }
					: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
				themeSystem: 'bootstrap5',
				editable: true,
				selectable: true,
				selectMirror: true,
				dayMaxEvents: true,
				events: '/calendar/events',
				eventResizableFromStart: true,
				eventDurationEditable: true,
				select: function(info) {
					const title = prompt('Add event title for ' + info.startStr);
					if(title) {
						fetch('/calendar/events/store', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								'X-CSRF-TOKEN': '{{ csrf_token() }}'
							},
							body: JSON.stringify({
								title: title,
								start: info.startStr,
								end: info.endStr,
								allDay: info.allDay
							})
						})
						.then(res => res.json())
						.then(event => {
							calendar.addEvent(event);
						});
					}
					calendar.unselect();
				},
				eventClick: function(info) {
					// Disable default click handler (handled by eventDidMount)
				},
				eventDidMount: function(info) {
					let clickTimer = null;
					info.el.addEventListener('click', function(e) {
						if (clickTimer) return;
						clickTimer = setTimeout(() => {
							clickTimer = null;
							// Single click: show event details
							alert(
								'Title: ' + info.event.title
							);
						}, 250);
					});
					info.el.addEventListener('dblclick', function(e) {
						if (clickTimer) {
							clearTimeout(clickTimer);
							clickTimer = null;
						}
						// Double click: show CRUD options
						const action = prompt(
							'Type:\ncreate - to add new event\nedit - to edit this event\ndelete - to delete this event'
						);
						if (action === 'create') {
							const title = prompt('New event title:');
							if (!title) return;
							const date = prompt('Date (YYYY-MM-DD):', info.event.startStr);
							if (!date) return;
							fetch('/calendar/events/store', {
								method: 'POST',
								headers: {
									'Content-Type': 'application/json',
									'X-CSRF-TOKEN': '{{ csrf_token() }}'
								},
								body: JSON.stringify({ title: title, start: date })
							})
							.then(res => res.json())
							.then(event => {
								calendar.addEvent(event);
							});
						} else if (action === 'edit') {
							const newTitle = prompt('Edit event title:', info.event.title);
							if (newTitle && newTitle !== info.event.title) {
								fetch('/calendar/events/update', {
									method: 'POST',
									headers: {
										'Content-Type': 'application/json',
										'X-CSRF-TOKEN': '{{ csrf_token() }}'
									},
									body: JSON.stringify({ id: info.event.id, title: newTitle })
								})
								.then(() => {
									info.event.setProp('title', newTitle);
								});
							}
						} else if (action === 'delete') {
							if (confirm('Delete event "' + info.event.title + '" ?')) {
								fetch('/calendar/events/delete', {
									method: 'POST',
									headers: {
										'Content-Type': 'application/json',
										'X-CSRF-TOKEN': '{{ csrf_token() }}'
									},
									body: JSON.stringify({ id: info.event.id })
								})
								.then(() => {
									info.event.remove();
								});
							}
						}
					});
				},
				eventDrop: function(info) {
					// Update event date after drag
					fetch('/calendar/events/update', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': '{{ csrf_token() }}'
						},
						body: JSON.stringify({
							id: info.event.id,
							start: info.event.startStr,
							end: info.event.endStr
						})
					});
				},
				eventResize: function(info) {
					// Fix: subtract 1 day from end date (exclusive)
					let endDate = info.event.end;
					if (endDate) {
						endDate = new Date(endDate);
						endDate.setDate(endDate.getDate() - 1);
						const yyyy = endDate.getFullYear();
						const mm = String(endDate.getMonth() + 1).padStart(2, '0');
						const dd = String(endDate.getDate()).padStart(2, '0');
						endDate = `${yyyy}-${mm}-${dd}T${String(endDate.getHours()).padStart(2, '0')}:${String(endDate.getMinutes()).padStart(2, '0')}:${String(endDate.getSeconds()).padStart(2, '0')}`;
					} else {
						endDate = info.event.endStr;
					}
					fetch('/calendar/events/update', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': '{{ csrf_token() }}'
						},
						body: JSON.stringify({
							id: info.event.id,
							start: info.event.startStr,
							end: endDate
						})
					});
				}
			});
			calendar.render();

			document.getElementById('add-event-btn').addEventListener('click', function(){
				const title = prompt('Event title:');
				if(!title) return;
				const date = prompt('Date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
				if(!date) return;
				fetch('/calendar/events/store', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},
					body: JSON.stringify({ title: title, start: date })
				})
				.then(res => res.json())
				.then(event => {
					calendar.addEvent(event);
				});
			});
		});

		/* -------------------------
		   Digital Clock (neon)
		   ------------------------- */
		function updateDigitalClock() {
			const el = document.getElementById('digital-clock');
			if(!el) return;
			const now = new Date();
			// Format HH:MM:SS AM/PM
			let hh = now.getHours();
			const ampm = hh >= 12 ? 'PM' : 'AM';
			hh = hh % 12; hh = hh ? hh : 12;
			const mm = String(now.getMinutes()).padStart(2,'0');
			const ss = String(now.getSeconds()).padStart(2,'0');
			el.textContent = `${hh}:${mm}:${ss} ${ampm}`;
		}
		setInterval(updateDigitalClock, 1000);
		updateDigitalClock();

		/* -------------------------
		   Analog clock (canvas)
		   ------------------------- */
		(function(){
			const canvas = document.getElementById('analog-clock');
			if(!canvas) return;
			const ctx = canvas.getContext('2d');
			const w = canvas.width, h = canvas.height;
			const cx = w/2, cy = h/2;
			const radius = Math.min(cx, cy) - 10;

			function drawFace() {
				// Outer gradient glow
				const grad = ctx.createRadialGradient(cx, cy, radius*0.1, cx, cy, radius*1.1);
				grad.addColorStop(0, 'rgba(255,255,255,0.06)');
				grad.addColorStop(0.4, 'rgba(0,255,200,0.06)');
				grad.addColorStop(1, 'rgba(0,0,0,0.6)');

				ctx.beginPath();
				ctx.arc(cx, cy, radius + 8, 0, 2*Math.PI);
				ctx.fillStyle = grad;
				ctx.fill();

				// main dial
				ctx.beginPath();
				ctx.arc(cx, cy, radius, 0, 2*Math.PI);
				ctx.fillStyle = '#0b0d10';
				ctx.fill();

				// ticks
				ctx.strokeStyle = 'rgba(255,255,255,0.12)';
				for(let i=0;i<60;i++){
					const ang = i * Math.PI / 30;
					const r1 = radius - (i%5 === 0 ? 20 : 12);
					const x1 = cx + Math.cos(ang) * (radius - 4);
					const y1 = cy + Math.sin(ang) * (radius - 4);
					const x2 = cx + Math.cos(ang) * r1;
					const y2 = cy + Math.sin(ang) * r1;
					ctx.lineWidth = (i%5===0?3:1);
					ctx.beginPath();
					ctx.moveTo(x1,y1);
					ctx.lineTo(x2,y2);
					ctx.stroke();
				}

				// numbers
				ctx.fillStyle = '#fff';
				ctx.font = `${Math.round(radius*0.14)}px Arial`;
				ctx.textAlign = 'center';
				ctx.textBaseline = 'middle';
				for(let n=1;n<=12;n++){
					const ang = (n * Math.PI / 6) - Math.PI/2;
					const x = cx + Math.cos(ang) * (radius - 40);
					const y = cy + Math.sin(ang) * (radius - 40);
					ctx.fillText(String(n), x, y);
				}
			}

			function drawHands() {
				const now = new Date();
				let sec = now.getSeconds();
				let min = now.getMinutes();
				let hr  = now.getHours();
				hr = hr % 12;

				// hour
				const hourAngle = (hr + min/60 + sec/3600) * Math.PI/6 - Math.PI/2;
				ctx.beginPath();
				ctx.lineWidth = 8;
				ctx.lineCap = 'round';
				ctx.strokeStyle = '#f6c23e';
				ctx.moveTo(cx,cy);
				ctx.lineTo(cx + Math.cos(hourAngle)*(radius*0.5), cy + Math.sin(hourAngle)*(radius*0.5));
				ctx.stroke();

				// minute
				const minuteAngle = (min + sec/60) * Math.PI/30 - Math.PI/2;
				ctx.beginPath();
				ctx.lineWidth = 5;
				ctx.strokeStyle = '#36b9cc';
				ctx.moveTo(cx,cy);
				ctx.lineTo(cx + Math.cos(minuteAngle)*(radius*0.75), cy + Math.sin(minuteAngle)*(radius*0.75));
				ctx.stroke();

				// second
				const secondAngle = sec * Math.PI/30 - Math.PI/2;
				ctx.beginPath();
				ctx.lineWidth = 2;
				ctx.strokeStyle = '#ff6b6b';
				ctx.moveTo(cx,cy);
				ctx.lineTo(cx + Math.cos(secondAngle)*(radius*0.85), cy + Math.sin(secondAngle)*(radius*0.85));
				ctx.stroke();

				// center
				ctx.beginPath();
				ctx.arc(cx, cy, 6, 0, 2*Math.PI);
				ctx.fillStyle = '#fff';
				ctx.fill();
				ctx.beginPath();
				ctx.arc(cx, cy, 3, 0, 2*Math.PI);
				ctx.fillStyle = '#ff4757';
				ctx.fill();
			}

			function render() {
				ctx.clearRect(0,0,w,h);
				drawFace();
				drawHands();
			}

			setInterval(render, 1000);
			render();
		})();

	</script>
@endpush
