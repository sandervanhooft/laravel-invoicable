<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('invoicable');
            $table->integer('tax')->default(0)->description('in cents');
            $table->integer('total')->default(0)->description('in cents');
            $table->char('currency', 3);
            $table->char('reference', 17);
            $table->char('status', 16)->nullable();
            $table->text('receiver_info')->nullable();
            $table->text('sender_info')->nullable();
            $table->text('payment_info')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('amount')->default(0)->description('in cents, including tax');
            $table->integer('tax')->default(0)->description('in cents');
            $table->float('tax_percentage')->default(0);
            $table->integer('invoice_id')->unsigned();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->char('description', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoices');
    }
}
