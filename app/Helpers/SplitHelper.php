<?php

class SplitHelper {

    public static function splitData($query) {
        $collections = collect([]);

        // Nilai default untuk item ketika tidak ada penerima kuasa


        foreach ($query as $item) {

            // Nilai default untuk item ketika tidak ada penerima kuasa
            $defaultValues = [
                'id' => $item->id,
                'no_surat' => str_contains($item->no_surat, 'SK') ? $item->no_surat : '-',
                'branch_id' => $item->branch_id,
                'status' => $item->status,
                'file' => $item->file,
                'penerima_kuasa' => 'Central - KP',
                'branches' => $item->branches
            ];
            $penerima_kuasa = $item->penerima_kuasa()->get();

            // Jika ada penerima kuasa
            if ($penerima_kuasa->count() > 0) {
                // Buat array sementara untuk menampung item yang telah diubah posisinya
                $tempCollections = [];

                // Jika BM ada, letakkan di posisi pertama
                $bmAdded = false;

                foreach ($penerima_kuasa as $penerima) {
                    $tempItem = array_merge($defaultValues, [
                        'id' => $item->id,
                        'no_surat' => str_contains($item->no_surat, 'SK') ? $item->no_surat : '-',
                        'branch_id' => $item->branch_id,
                        'status' => $item->status,
                        'file' => $item->file,
                        'penerima_kuasa' => '[' . $penerima->getPosition() . ']' . ' ' . $penerima->name,
                        'branches' => $item->branches
                    ]);

                    // Jika 'BM' belum ditambahkan dan saat ini adalah 'BM',
                    // tambahkan 'BM' ke koleksi di posisi pertama
                    if (!$bmAdded && $penerima->getPosition() === 'BM') {
                        array_unshift($tempCollections, $tempItem);
                        $bmAdded = true;
                    } else {
                        // Tambahkan item ke $tempCollections untuk swap nanti
                        $tempCollections[] = $tempItem;
                    }
                }

                // Menukar posisi item pada $tempCollections (mulai dari item ke-9)
                $count = count($tempCollections);
                for ($i = 8; $i < $count; $i += 2) {
                    if ($i + 1 < $count) {
                        $temp = $tempCollections[$i];
                        $tempCollections[$i] = $tempCollections[$i + 1];
                        $tempCollections[$i + 1] = $temp;
                    }
                }

                // Menambahkan item yang telah ditukar ke $collections
                $collections = $collections->merge(collect($tempCollections));
            } else {
                // Jika tidak ada penerima kuasa, tambahkan item dengan nilai default (null)
                $collections->push($defaultValues);
            }
        }
        return $collections;
    }
}
