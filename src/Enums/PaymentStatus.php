<?php

namespace Morningtrain\NETSEasy\Enums;

enum PaymentStatus: int
{
    case INITIATED = 0;
    case CREATED = 1;
    case TERMINATED = 2;
    case CHECKOUT_COMPLETED = 3;
    case RESERVED = 4;
    case RESERVE_FAILED = 5;
    case PARTLY_CHARGE_CREATED = 6;
    case PARTLY_CHARGE_FAILED = 7;
    case CHARGE_CREATED = 8;
    case CHARGE_FAILED = 9;
    case PARTLY_REFUND_INITIATED = 10;
    case PARTLY_REFUND_FAILED = 11;
    case PARTLY_REFUND_COMPLETED = 12;
    case REFUND_INITIATED = 13;
    case REFUND_FAILED = 14;
    case REFUND_COMPLETED = 15;
    case CANCEL_CREATED = 16;
    case CANCEL_FAILED = 17;
}
