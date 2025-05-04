<!-- suppliers/partials/orders_table.blade.php -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Liste des commandes</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Référence</th>
                    <th>Date</th>
                    <th>Fournisseur</th>
                    <th>Articles</th>
                    <th>Quantité</th>
                    <th>Montant</th>    
                    <th>Statut</th>
                    <th>Réception</th>
                    <th>Facturation</th>
                    <th>Échéance</th>
                </tr>
            </thead>

                <tbody>
                    @forelse($orders as $order)
                        <tr class="{{ (isset($order['per_received']) && $order['per_received'] == 100 && isset($order['per_billed']) && $order['per_billed'] == 100) ? 'table-success' : '' }}">
                            <td>
                                <strong>{{ $order['name'] ?? 'N/A' }}</strong>
                                @if(isset($order['creation']))
                                    <br><small>Créé le: {{ \Carbon\Carbon::parse($order['creation'])->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td>
                                @if(isset($order['transaction_date']))
                                    {{ \Carbon\Carbon::parse($order['transaction_date'])->format('d/m/Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                {{ $order['supplier'] ?? 'N/A' }}
                                @if(isset($order['supplier_name']))
                                    <br>{{ $order['supplier_name'] }}
                                @endif
                            </td>
                            <td>
                                @if(isset($order['items']) && is_array($order['items']) && count($order['items']) > 0)
                              <!--<span class="badge badge-info">{{ count($order['items']) }} article(s)</span> -->
                                    <ul class="small list-unstyled mb-0">
                                        @foreach(array_slice($order['items'], 0, 2) as $item)
                                            <li>{{ $item['item_code'] ?? 'Item' }}</li>
                                        @endforeach
                                        @if(count($order['items']) > 2) 
                                            <li>... et {{ count($order['items']) - 2 }} autre(s)</li>
                                        @endif
                                    </ul>
                                @else
                                    Aucun article
                                @endif
                            </td>
                            <td>
                            @php
                                $totalQty = 0;
                                if (isset($order['items']) && is_array($order['items'])) {
                                    foreach ($order['items'] as $item) {
                                        $totalQty += $item['qty'] ?? 0;
                                    }
                                }
                            @endphp
                            {{ $totalQty }}
                        </td>

                            <td>
                                <strong>{{ number_format($order['grand_total'] ?? 0, 2) }} {{ $order['currency'] ?? '' }}</strong>
                                @if(isset($order['taxes']) && is_array($order['taxes']) && count($order['taxes']) > 0)
                                    <br><small>Dont taxes: {{ number_format($order['total_taxes_and_charges'] ?? 0, 2) }}</small>
                                @endif
                            </td>
                            <td>
               
                            @php
                                $status = $order['status'] ?? 'Inconnu';

                                $statusClass = 'badge-danger';

                                if ($status == 'To Receive and Bill') {
                                    $statusClass = 'badge-warning';
                                    $status = 'À recevoir et facturer';
                                } elseif ($status == 'Completed') {
                                    $statusClass = 'badge-success';
                                    $status = 'Terminée';
                                } elseif ($status == 'To Receive') {
                                    $statusClass = 'badge-info';
                                    $status = 'À recevoir';
                                } elseif ($status == 'To Bill') {
                                    $statusClass = 'badge-primary';
                                    $status = 'À facturer';
                                }
                            @endphp
                            <span class="badge {{ $statusClass }}" style="color: #000;">{{ $status }}</span>



                            </td>
                            <td>
                                @php
                                    $perReceived = $order['per_received'] ?? 0;
                                    $receivedClass = $perReceived == 100 ? 'bg-success' : 'bg-warning';
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar progress-bar-striped {{ $receivedClass }}" role="progressbar" 
                                         style="width: {{ $perReceived }}%;" aria-valuenow="{{ $perReceived }}" 
                                         aria-valuemin="0" aria-valuemax="100">{{ $perReceived }}%</div>
                                </div>
                                @if($perReceived == 100)
                                    <small class="text-success">Reçu complet</small>
                                @else
                                    <small class="text-warning">En attente</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $perBilled = $order['per_billed'] ?? 0;
                                    $billedClass = $perBilled == 100 ? 'bg-success' : 'bg-warning';
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar progress-bar-striped {{ $billedClass }}" role="progressbar" 
                                         style="width: {{ $perBilled }}%;" aria-valuenow="{{ $perBilled }}" 
                                         aria-valuemin="0" aria-valuemax="100">{{ $perBilled }}%</div>
                                </div>
                                @if($perBilled == 100)
                                    <small class="text-success">Facturé complet</small>
                                @else
                                    <small class="text-warning">Non facturé</small>
                                @endif
                            </td>
                            <td>
                                @if(isset($order['schedule_date']))
                                    {{ \Carbon\Carbon::parse($order['schedule_date'])->format('d/m/Y') }}
                                    @php
                                        $daysUntilDeadline = \Carbon\Carbon::parse($order['schedule_date'])->diffInDays(now(), false);
                                    @endphp
                                    @if($daysUntilDeadline < 0)
                                        <br><span class="badge bg-danger text-white">Retard de {{ abs($daysUntilDeadline) }} jour(s)</span>
                                    @elseif($daysUntilDeadline <= 7)
                                        <br><span class="badge bg-warning text-dark">Dans {{ $daysUntilDeadline }} jour(s)</span>
                                    @else
                                        <br><span class="badge bg-info text-dark">Dans {{ $daysUntilDeadline }} jour(s)</span>
                                    @endif

                                @else
                                    N/A
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Aucune commande trouvée pour ce fournisseur</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-end"><strong>Total général</strong></td>
                        <td>
                            <strong>{{ number_format(collect($orders)->sum('grand_total'), 2) }} {{ $orders[0]['currency'] ?? '' }}</strong>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>


            </table>
        </div>
    </div>
</div>