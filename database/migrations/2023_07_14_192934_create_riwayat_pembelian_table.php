<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiwayatPembelianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riwayat_pembelian', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('barang_id');
            $table->string('nama_barang');
            $table->string('kode_barang');
            $table->string('perusahaan_id');
            $table->string('nama_perusahaan');
            $table->integer('harga_barang');
            $table->integer('jumlah_barang');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('user')->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('riwayat_pembelian');
    }
}